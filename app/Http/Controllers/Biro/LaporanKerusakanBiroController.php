<?php

namespace App\Http\Controllers\Biro;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use App\Models\RiwayatAktivitas;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanKerusakanBiroController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $urgensi = $request->query('urgensi');

        $query = LaporanKerusakan::with(['user', 'ruangan', 'fasilitas', 'penanggungJawab']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($urgensi) {
            $query->where('tingkat_urgensi', $urgensi);
        }

        $laporanList = $query->latest()->paginate(15)->withQueryString();

        return view('biro.laporan.index', compact('laporanList', 'status', 'urgensi'));
    }

    public function show(LaporanKerusakan $laporan)
    {
        $laporan->load(['user', 'ruangan', 'fasilitas', 'penanggungJawab']);
        return view('biro.laporan.show', compact('laporan'));
    }

    public function updateStatus(Request $request, LaporanKerusakan $laporan)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,sedang_ditangani,selesai,ditolak'],
            'catatan_tindak_lanjut' => ['nullable', 'string', 'max:1000'],
            'ubah_status_ruangan' => ['nullable', 'in:perawatan,aktif'],
        ]);

        $laporan->status = $validated['status'];
        $laporan->catatan_tindak_lanjut = $validated['catatan_tindak_lanjut'] ?? null;
        $laporan->ditangani_oleh = Auth::id();

        if ($validated['status'] === 'selesai') {
            $laporan->selesai_pada = now();
        }

        $laporan->save();

        // Opsi otomatis ubah status ruangan ke 'perawatan' jika kerusakan parah
        if (!empty($validated['ubah_status_ruangan'])) {
            $ruangan = $laporan->ruangan;
            $ruangan->status_operasional = $validated['ubah_status_ruangan'];
            $ruangan->save();

            RiwayatAktivitas::catat('STATUS_RUANGAN_SYNC', "Status ruangan {$ruangan->kode_ruangan} diubah ke {$validated['ubah_status_ruangan']} berkaitan laporan {$laporan->kode_laporan}");
        }

        RiwayatAktivitas::catat('UPDATE_LAPORAN', "Biro memperbarui status laporan {$laporan->kode_laporan} menjadi {$validated['status']}");

        // Kirim Notifikasi ke User pelapor
        $tanggapanText = !empty($laporan->catatan_tindak_lanjut) ? " Catatan: '{$laporan->catatan_tindak_lanjut}'." : "";
        \App\Models\Notifikasi::kirimKeUser(
            $laporan->user_id,
            'Laporan Kerusakan Ditanggapi',
            "Laporan Anda (Kode: {$laporan->kode_laporan}) mengenai '{$laporan->judul_laporan}' telah ditanggapi oleh Biro dengan status: " . strtoupper($laporan->status) . ".{$tanggapanText}",
            'laporan_ditanggapi',
            route('laporan.show', $laporan)
        );

        return back()->with('success', "Status laporan {$laporan->kode_laporan} berhasil diperbarui.");
    }
}
