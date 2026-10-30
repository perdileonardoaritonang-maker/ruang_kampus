<?php

namespace App\Http\Controllers\Biro;

use App\Http\Controllers\Controller;
use App\Models\JadwalTetap;
use App\Models\RiwayatAktivitas;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalTetapController extends Controller
{
    public function index(Request $request)
    {
        $ruanganId = $request->query('ruangan_id');
        $hari = $request->query('hari');
        $jenis = $request->query('jenis');

        $query = JadwalTetap::with(['ruangan', 'creator']);

        if ($ruanganId) {
            $query->where('ruangan_id', $ruanganId);
        }

        if ($hari) {
            $query->where('hari', $hari);
        }

        if ($jenis) {
            $query->where('jenis_jadwal', $jenis);
        }

        $jadwalList = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(15)->withQueryString();
        $allRuangan = Ruangan::orderBy('lantai')->orderBy('kode_ruangan')->get();

        return view('biro.jadwal.index', compact('jadwalList', 'allRuangan', 'ruanganId', 'hari', 'jenis'));
    }

    public function create()
    {
        $allRuangan = Ruangan::where('status_operasional', 'aktif')->orderBy('lantai')->orderBy('kode_ruangan')->get();
        return view('biro.jadwal.create', compact('allRuangan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruangan_id' => ['required', 'exists:ruangan,id'],
            'nama_kegiatan' => ['required', 'string', 'max:150'],
            'jenis_jadwal' => ['required', 'in:kuliah,praktikum,acara_kampus,perawatan,penutupan_sementara'],
            'tipe_pengulangan' => ['required', 'in:tidak_berulang,mingguan'],
            'hari' => ['nullable', 'required_if:tipe_pengulangan,mingguan', 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu'],
            'tanggal_spesifik' => ['nullable', 'required_if:tipe_pengulangan,tidak_berulang', 'date'],
            'tanggal_mulai_efektif' => ['required', 'date'],
            'tanggal_selesai_efektif' => ['required', 'date', 'after_or_equal:tanggal_mulai_efektif'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $startFmt = strlen($validated['jam_mulai']) === 5 ? $validated['jam_mulai'] . ':00' : $validated['jam_mulai'];
        $endFmt   = strlen($validated['jam_selesai']) === 5 ? $validated['jam_selesai'] . ':00' : $validated['jam_selesai'];

        // 1. Cek apakah bertabrakan dengan Jadwal Tetap lain yang sudah ada
        $conflictJadwal = JadwalTetap::where('ruangan_id', $validated['ruangan_id'])
            ->where(function ($q) use ($validated) {
                if ($validated['tipe_pengulangan'] === 'mingguan') {
                    $q->where('hari', $validated['hari']);
                } else {
                    $q->where('tanggal_spesifik', $validated['tanggal_spesifik']);
                }
            })
            ->where('tanggal_mulai_efektif', '<=', $validated['tanggal_selesai_efektif'])
            ->where('tanggal_selesai_efektif', '>=', $validated['tanggal_mulai_efektif'])
            ->where(function ($q) use ($startFmt, $endFmt) {
                $q->where('jam_mulai', '<', $endFmt)
                  ->where('jam_selesai', '>', $startFmt);
            })
            ->first();

        if ($conflictJadwal) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'jam_mulai' => [
                    "⚠️ PERINGATAN JADWAL NABRAK: Ruangan ini sudah memiliki jadwal '{$conflictJadwal->nama_kegiatan}' pada pukul " . substr($conflictJadwal->jam_mulai, 0, 5) . " - " . substr($conflictJadwal->jam_selesai, 0, 5) . " WIB. Mohon gunakan slot jam lain atau ruangan lain."
                ],
            ]);
        }

        // 2. Cek apakah bertabrakan dengan Peminjaman yang sudah disetujui
        $conflictBooking = \App\Models\Peminjaman::with('user')
            ->where('ruangan_id', $validated['ruangan_id'])
            ->where('status', 'disetujui')
            ->where('tanggal', '>=', $validated['tanggal_mulai_efektif'])
            ->where('tanggal', '<=', $validated['tanggal_selesai_efektif'])
            ->where(function ($q) use ($startFmt, $endFmt) {
                $q->where('jam_mulai', '<', $endFmt)
                  ->where('jam_selesai', '>', $startFmt);
            })
            ->first();

        if ($conflictBooking) {
            $peminjamName = $conflictBooking->user ? $conflictBooking->user->nama_lengkap : 'Peminjam';
            throw \Illuminate\Validation\ValidationException::withMessages([
                'jam_mulai' => [
                    "⚠️ PERINGATAN JADWAL NABRAK: Ruangan ini sudah dipinjam oleh {$peminjamName} pada tanggal {$conflictBooking->tanggal->format('d/m/Y')} pukul " . substr($conflictBooking->jam_mulai, 0, 5) . " - " . substr($conflictBooking->jam_selesai, 0, 5) . " WIB (Kode: {$conflictBooking->kode_booking})."
                ],
            ]);
        }

        $jadwal = JadwalTetap::create([
            'ruangan_id' => $validated['ruangan_id'],
            'nama_kegiatan' => trim($validated['nama_kegiatan']),
            'jenis_jadwal' => $validated['jenis_jadwal'],
            'tipe_pengulangan' => $validated['tipe_pengulangan'],
            'hari' => $validated['tipe_pengulangan'] === 'mingguan' ? $validated['hari'] : null,
            'tanggal_spesifik' => $validated['tipe_pengulangan'] === 'tidak_berulang' ? $validated['tanggal_spesifik'] : null,
            'tanggal_mulai_efektif' => $validated['tanggal_mulai_efektif'],
            'tanggal_selesai_efektif' => $validated['tanggal_selesai_efektif'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'keterangan' => $validated['keterangan'] ?? null,
            'created_by' => Auth::id(),
        ]);

        RiwayatAktivitas::catat('CREATE_JADWAL_TETAP', "Menambahkan jadwal tetap: {$jadwal->nama_kegiatan} pada ruangan ID {$jadwal->ruangan_id}");

        return redirect()->route('biro.jadwal.index')->with('success', "Jadwal tetap '{$jadwal->nama_kegiatan}' berhasil disimpan.");
    }

    public function destroy(JadwalTetap $jadwal)
    {
        $nama = $jadwal->nama_kegiatan;
        $jadwal->delete();

        RiwayatAktivitas::catat('DELETE_JADWAL_TETAP', "Menghapus jadwal tetap: {$nama}");

        return back()->with('success', "Jadwal '{$nama}' berhasil dihapus.");
    }
}
