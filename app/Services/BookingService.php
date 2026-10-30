<?php

namespace App\Services;

use App\Models\JadwalTetap;
use App\Models\Peminjaman;
use App\Models\RiwayatAktivitas;
use App\Models\Ruangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Map nama hari Carbon (English) ke Bahasa Indonesia pada jadwal tetap.
     */
    public static function convertDayToIndonesian(int $dayOfWeek): string
    {
        $days = [
            0 => 'minggu',
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
        ];

        return $days[$dayOfWeek] ?? 'senin';
    }

    /**
     * Periksa apakah suatu ruangan memiliki bentrok jadwal pada rentang tanggal dan jam tertentu.
     * Menggunakan logika interval overlap: (jam_mulai < rentang_selesai) AND (jam_selesai > rentang_mulai).
     *
     * @return array [ 'conflict' => bool, 'reason' => string|null ]
     */
    public function checkConflict(int $ruanganId, string $tanggal, string $jamMulai, string $jamSelesai, ?int $ignorePeminjamanId = null): array
    {
        $ruangan = Ruangan::find($ruanganId);
        if (!$ruangan || $ruangan->status_operasional !== 'aktif') {
            return [
                'conflict' => true,
                'reason' => 'Ruangan tidak aktif atau sedang dalam perawatan.',
            ];
        }

        // Standardize format jam to H:i:s
        $start = strlen($jamMulai) === 5 ? $jamMulai . ':00' : $jamMulai;
        $end = strlen($jamSelesai) === 5 ? $jamSelesai . ':00' : $jamSelesai;

        // 1. Cek bentrok dengan Peminjaman lain yang statusnya masih 'disetujui'
        $peminjamanConflict = Peminjaman::where('ruangan_id', $ruanganId)
            ->whereDate('tanggal', $tanggal)
            ->where('status', 'disetujui')
            ->when($ignorePeminjamanId, fn($q) => $q->where('id', '!=', $ignorePeminjamanId))
            ->where(function ($q) use ($start, $end) {
                // Rumus Overlap: StartA < EndB AND EndA > StartB
                $q->where('jam_mulai', '<', $end)
                  ->where('jam_selesai', '>', $start);
            })
            ->first();

        if ($peminjamanConflict) {
            return [
                'conflict' => true,
                'reason' => "Terdapat peminjaman lain pada pukul {$peminjamanConflict->jam_mulai} - {$peminjamanConflict->jam_selesai} (Kode: {$peminjamanConflict->kode_booking}).",
            ];
        }

        // 2. Cek bentrok dengan Jadwal Tetap Kampus (Kuliah / Praktikum / Perawatan)
        $dateObj = Carbon::parse($tanggal);
        $hariIndo = self::convertDayToIndonesian($dateObj->dayOfWeek);

        $jadwalConflict = JadwalTetap::where('ruangan_id', $ruanganId)
            ->where(function ($q) use ($dateObj, $hariIndo) {
                // Jadwal Mingguan
                $q->where(function ($sub) use ($dateObj, $hariIndo) {
                    $sub->where('tipe_pengulangan', 'mingguan')
                        ->where('hari', $hariIndo)
                        ->where('tanggal_mulai_efektif', '<=', $dateObj->toDateString())
                        ->where('tanggal_selesai_efektif', '>=', $dateObj->toDateString());
                })
                // Atau Jadwal Khusus Tanggal Spesifik
                ->orWhere(function ($sub) use ($dateObj) {
                    $sub->where('tipe_pengulangan', 'tidak_berulang')
                        ->where('tanggal_spesifik', $dateObj->toDateString());
                });
            })
            ->where(function ($q) use ($start, $end) {
                $q->where('jam_mulai', '<', $end)
                  ->where('jam_selesai', '>', $start);
            })
            ->first();

        if ($jadwalConflict) {
            return [
                'conflict' => true,
                'reason' => "Terdapat jadwal tetap kampus: '{$jadwalConflict->nama_kegiatan}' ({$jadwalConflict->jam_mulai} - {$jadwalConflict->jam_selesai}).",
            ];
        }

        return [
            'conflict' => false,
            'reason' => null,
        ];
    }

    /**
     * Eksekusi pengajuan peminjaman ruangan dengan transaksi database & Pesimistic Locking (lockForUpdate).
     */
    public function createBooking(User $user, array $data): Peminjaman
    {
        // 1. Validasi Status Akun Pemohon
        if (!$user->canBookRoom()) {
            throw ValidationException::withMessages([
                'user' => ['Akun Anda belum memenuhi syarat untuk meminjam ruangan (pastikan email terverifikasi, identitas disetujui Biro, dan akun aktif).'],
            ]);
        }

        // Standardisasi format waktu agar pengecekan overlap konsisten.
        $newStart = strlen($data['jam_mulai']) === 5 ? $data['jam_mulai'] . ':00' : $data['jam_mulai'];
        $newEnd   = strlen($data['jam_selesai']) === 5 ? $data['jam_selesai'] . ':00' : $data['jam_selesai'];

        // Transaksi mengunci akun peminjam supaya dua pengajuan serentak pada
        // ruangan berbeda tidak dapat melewati pemeriksaan bentrok pengguna.
        return DB::transaction(function () use ($user, $data, $newStart, $newEnd) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            // Satu pengguna hanya boleh memiliki satu ruangan pada waktu yang overlap.
            $conflictingUserBooking = Peminjaman::with('ruangan')
                ->where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereDate('tanggal', $data['tanggal'])
                ->where('jam_mulai', '<', $newEnd)
                ->where('jam_selesai', '>', $newStart)
                ->first();

            if ($conflictingUserBooking) {
                throw ValidationException::withMessages([
                    'booking' => [
                        "Anda sudah memiliki peminjaman yang aktif pada waktu yang sama: " .
                        "{$conflictingUserBooking->ruangan->kode_ruangan} (" .
                        substr($conflictingUserBooking->jam_mulai, 0, 5) . ' - ' .
                        substr($conflictingUserBooking->jam_selesai, 0, 5) . "). " .
                        "Pilih slot jam yang berbeda agar tidak bentrok."
                    ],
                ]);
            }

            // Kunci ruangan dan periksa bentrok dengan peminjaman pengguna lain.
            // Lock record ruangan
            $ruangan = Ruangan::where('id', $data['ruangan_id'])->lockForUpdate()->firstOrFail();

            if ($ruangan->status_operasional !== 'aktif') {
                throw ValidationException::withMessages([
                    'ruangan_id' => ['Ruangan yang dipilih sedang tidak berstatus aktif atau dalam pemeliharaan.'],
                ]);
            }

            if ($data['jumlah_peserta'] > $ruangan->kapasitas) {
                throw ValidationException::withMessages([
                    'jumlah_peserta' => ["Jumlah peserta ({$data['jumlah_peserta']}) melebihi kapasitas maksimum ruangan ({$ruangan->kapasitas} orang)."],
                ]);
            }

            // Periksa bentrok slot waktu
            $conflictCheck = $this->checkConflict(
                $ruangan->id,
                $data['tanggal'],
                $data['jam_mulai'],
                $data['jam_selesai']
            );

            if ($conflictCheck['conflict']) {
                throw ValidationException::withMessages([
                    'waktu' => [$conflictCheck['reason']],
                ]);
            }

            // Generate Kode Booking Unik
            $todayCode = date('Ymd');
            $countToday = Peminjaman::whereDate('created_at', today())->count() + 1;
            $kodeBooking = 'BKG-' . $todayCode . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            // Simpan Peminjaman (Otomatis Disetujui)
            $peminjaman = Peminjaman::create([
                'kode_booking' => $kodeBooking,
                'user_id' => $user->id,
                'ruangan_id' => $ruangan->id,
                'tanggal' => $data['tanggal'],
                'jam_mulai' => $data['jam_mulai'],
                'jam_selesai' => $data['jam_selesai'],
                'jumlah_peserta' => $data['jumlah_peserta'],
                'alasan_peminjaman' => trim($data['alasan_peminjaman']),
                'catatan_tambahan' => !empty($data['catatan_tambahan']) ? trim($data['catatan_tambahan']) : null,
                'status' => 'disetujui',
            ]);

            // Audit Trail Log
            RiwayatAktivitas::catat(
                'BOOKING_CREATED',
                "Peminjaman ruangan {$ruangan->nama_ruangan} ({$peminjaman->kode_booking}) pada tanggal {$data['tanggal']} ({$data['jam_mulai']} - {$data['jam_selesai']}) berhasil diajukan dan disetujui otomatis.",
                $user->id
            );

            // Kirim Notifikasi ke Biro
            \App\Models\Notifikasi::kirimKeBiro(
                'Peminjaman Ruangan Baru',
                "Peminjaman baru diajukan oleh {$user->nama_lengkap} untuk ruangan {$ruangan->kode_ruangan} pada tanggal " . \Carbon\Carbon::parse($peminjaman->tanggal)->format('d/m/Y') . " pukul {$peminjaman->jam_mulai} - {$peminjaman->jam_selesai} WIB (Kode: {$peminjaman->kode_booking}).",
                'peminjaman_baru',
                route('biro.peminjaman.index')
            );

            return $peminjaman;
        });
    }

    /**
     * Batalkan peminjaman oleh User (Hanya sebelum jam mulai).
     */
    public function cancelByUser(Peminjaman $peminjaman, User $user): void
    {
        if ($peminjaman->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak membatalkan peminjaman ini.');
        }

        if ($peminjaman->status !== 'disetujui') {
            throw ValidationException::withMessages([
                'status' => ['Peminjaman ini sudah tidak berstatus disetujui.'],
            ]);
        }

        $cancelDeadline = $peminjaman->deadline_pembatalan;
        if (now()->gte($cancelDeadline)) {
            throw ValidationException::withMessages([
                'waktu' => [
                    "Peminjaman tidak dapat dibatalkan. Batas waktu pembatalan adalah 5 menit sebelum jam mulai kegiatan (maksimal pukul {$cancelDeadline->format('H:i')} WIB)."
                ],
            ]);
        }

        $peminjaman->status = 'dibatalkan_user';
        $peminjaman->dibatalkan_oleh = $user->id;
        $peminjaman->dibatalkan_pada = now();
        $peminjaman->save();

        RiwayatAktivitas::catat(
            'BOOKING_CANCELLED_USER',
            "Peminjaman {$peminjaman->kode_booking} dibatalkan mandiri oleh peminjam ({$user->nama_lengkap}).",
            $user->id
        );
    }

    /**
     * Batalkan peminjaman oleh Biro (Wajib menyertakan alasan resmi).
     */
    public function cancelByBiro(Peminjaman $peminjaman, User $biro, string $alasan): void
    {
        if (!$biro->isBiro()) {
            abort(403, 'Hanya Biro yang berwenang melakukan pembatalan administratif.');
        }

        $peminjaman->status = 'dibatalkan_biro';
        $peminjaman->alasan_pembatalan = trim($alasan);
        $peminjaman->dibatalkan_oleh = $biro->id;
        $peminjaman->dibatalkan_pada = now();
        $peminjaman->save();

        RiwayatAktivitas::catat(
            'BOOKING_CANCELLED_BIRO',
            "Peminjaman {$peminjaman->kode_booking} milik {$peminjaman->user->nama_lengkap} dibatalkan oleh Biro ({$biro->nama_lengkap}). Alasan: {$alasan}",
            $biro->id
        );

        // Kirim Notifikasi ke User peminjam
        \App\Models\Notifikasi::kirimKeUser(
            $peminjaman->user_id,
            'Peminjaman Dibatalkan Biro',
            "Peminjaman ruangan {$peminjaman->ruangan->nama_ruangan} (Kode: {$peminjaman->kode_booking}) telah dibatalkan resmi oleh Biro Pengelola. Alasan: '{$peminjaman->alasan_pembatalan}'.",
            'peminjaman_dibatalkan_biro',
            route('peminjaman.show', $peminjaman)
        );
    }
}
