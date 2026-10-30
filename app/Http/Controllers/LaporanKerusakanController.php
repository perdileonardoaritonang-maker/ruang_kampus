<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\LaporanKerusakan;
use App\Models\RiwayatAktivitas;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanKerusakanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $laporanList = LaporanKerusakan::with(['ruangan', 'fasilitas'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('laporan.index', compact('laporanList'));
    }

    public function create(Request $request)
    {
        $selectedRuanganId = $request->query('ruangan_id');
        $allRuangan = Ruangan::orderBy('lantai')->orderBy('kode_ruangan')->get();
        $allFasilitas = Fasilitas::orderBy('nama_fasilitas')->get();

        return view('laporan.create', compact('selectedRuanganId', 'allRuangan', 'allFasilitas'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'ruangan_id' => ['required', 'exists:ruangan,id'],
            'fasilitas_id' => ['nullable', 'exists:fasilitas,id'],
            'judul_laporan' => ['required', 'string', 'max:150'],
            'deskripsi_masalah' => ['required', 'string', 'min:10', 'max:1500'],
            'tingkat_urgensi' => ['required', 'in:rendah,sedang,tinggi,darurat'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('laporan_foto', 'local');
        }

        $kodeLaporan = 'RPT-' . date('Ymd') . '-' . str_pad(LaporanKerusakan::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

        $laporan = LaporanKerusakan::create([
            'kode_laporan' => $kodeLaporan,
            'user_id' => $user->id,
            'ruangan_id' => $validated['ruangan_id'],
            'fasilitas_id' => $validated['fasilitas_id'] ?? null,
            'judul_laporan' => trim($validated['judul_laporan']),
            'deskripsi_masalah' => trim($validated['deskripsi_masalah']),
            'tingkat_urgensi' => $validated['tingkat_urgensi'],
            'foto' => $fotoPath,
            'status' => 'menunggu',
        ]);

        RiwayatAktivitas::catat('LAPORAN_CREATED', "Pelaporan kendala/kerusakan {$laporan->kode_laporan} dibuat oleh {$user->nama_lengkap}");

        // Kirim Notifikasi ke Biro
        \App\Models\Notifikasi::kirimKeBiro(
            'Laporan Kerusakan Fasilitas Baru',
            "Laporan kerusakan baru diajukan oleh {$user->nama_lengkap} untuk ruangan {$laporan->ruangan->kode_ruangan} (Judul: '{$laporan->judul_laporan}', Urgensi: " . strtoupper($laporan->tingkat_urgensi) . ").",
            'laporan_baru',
            route('biro.laporan.show', $laporan)
        );

        return redirect()->route('laporan.index')->with('success', "Laporan kerusakan fasilitas berhasil dikirim dengan kode: {$laporan->kode_laporan}");
    }

    public function show(LaporanKerusakan $laporan)
    {
        $user = Auth::user();
        if ($laporan->user_id !== $user->id && !$user->isBiro()) {
            abort(403, 'Akses Ditolak.');
        }

        $laporan->load(['ruangan', 'fasilitas', 'penanggungJawab', 'user']);

        return view('laporan.show', compact('laporan'));
    }

    /**
     * Stream a report image only to its owner or an authenticated Biro user.
     */
    public function foto(LaporanKerusakan $laporan)
    {
        $user = Auth::user();
        if ($laporan->user_id !== $user->id && !$user->isBiro()) {
            abort(403, 'Akses Ditolak.');
        }

        abort_if(!$laporan->foto || !Storage::disk('local')->exists($laporan->foto), 404);

        return Storage::disk('local')->response($laporan->foto, basename($laporan->foto), [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ], 'inline');
    }
}
