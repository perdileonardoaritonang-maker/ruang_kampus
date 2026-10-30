<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'kode_booking',
        'user_id',
        'ruangan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'jumlah_peserta',
        'alasan_peminjaman',
        'catatan_tambahan',
        'status',
        'alasan_pembatalan',
        'dibatalkan_oleh',
        'dibatalkan_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah_peserta' => 'integer',
            'dibatalkan_pada' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function pembuatPembatalan()
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh');
    }

    /**
     * Otomatis sinkronkan dan ubah status peminjaman yang telah terlewat menjadi 'selesai' (Kedaluwarsa),
     * serta kirim notifikasi ke user terkait.
     */
    public static function autoExpirePeminjaman(): int
    {
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        $toExpire = static::with('ruangan')
            ->where('status', 'disetujui')
            ->where(function ($q) use ($today, $currentTime) {
                $q->whereDate('tanggal', '<', $today)
                  ->orWhere(function ($sub) use ($today, $currentTime) {
                      $sub->whereDate('tanggal', $today)
                          ->where('jam_selesai', '<=', $currentTime);
                  });
            })
            ->get();

        $count = 0;
        foreach ($toExpire as $item) {
            $item->status = 'selesai';
            $item->save();
            $count++;

            // Kirim Notifikasi ke User bahwa waktu peminjaman telah habis
            if (class_exists(Notifikasi::class)) {
                Notifikasi::kirimKeUser(
                    $item->user_id,
                    'Waktu Peminjaman Telah Berakhir',
                    "Waktu peminjaman ruangan {$item->ruangan->nama_ruangan} (Kode: {$item->kode_booking}) telah selesai pada pukul " . substr($item->jam_selesai, 0, 5) . " WIB. Terima kasih telah menjaga kebersihan dan fasilitas ruangan kampus.",
                    'jam_pinjam_habis',
                    route('peminjaman.show', $item)
                );
            }
        }

        return $count;
    }

    /**
     * Waktu batas akhir pembatalan oleh user (5 menit sebelum jam mulai).
     */
    public function getDeadlinePembatalanAttribute(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_mulai)->subMinutes(5);
    }

    /**
     * Cek apakah waktu peminjaman telah kedaluwarsa / selesai.
     */
    public function isExpired(): bool
    {
        if ($this->status === 'selesai') {
            return true;
        }

        $endTime = \Carbon\Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_selesai);
        return now()->gte($endTime);
    }

    /**
     * Cek apakah peminjaman masih boleh dibatalkan oleh user.
     */
    public function bisaDibatalkan(): bool
    {
        if ($this->status !== 'disetujui') {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        return now()->lt($this->deadline_pembatalan);
    }

    /**
     * Cek apakah peminjaman masih aktif (belum selesai dan belum dibatalkan).
     */
    public function isActive(): bool
    {
        if ($this->status !== 'disetujui') {
            return false;
        }

        return !$this->isExpired();
    }
}
