<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PeminjamanController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Tampilkan formulir pengajuan peminjaman ruangan.
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        // Cek syarat akun
        if (!$user->canBookRoom()) {
            return redirect()->route('verification.identity.notice')
                ->with('warning', 'Anda belum dapat meminjam ruangan sebelum akun diverifikasi penuh oleh Biro.');
        }

        $selectedRuanganId = $request->query('ruangan_id');
        $selectedRuangan = $selectedRuanganId ? Ruangan::find($selectedRuanganId) : null;
        $allRuangan = Ruangan::where('status_operasional', 'aktif')->orderBy('lantai')->orderBy('kode_ruangan')->get();
        $tanggal = $request->query('tanggal', date('Y-m-d'));
        $jamMulai = $request->query('jam_mulai', '08:00');
        $jamSelesai = $request->query('jam_selesai', '10:00');

        return view('peminjaman.create', compact('selectedRuangan', 'allRuangan', 'tanggal', 'jamMulai', 'jamSelesai'));
    }

    /**
     * Simpan pengajuan peminjaman (Persetujuan Otomatis jika Lolos Validasi).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'ruangan_id' => ['required', 'exists:ruangan,id'],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'alasan_peminjaman' => ['required', 'string', 'min:10', 'max:1000'],
            'catatan_tambahan' => ['nullable', 'string', 'max:500'],
        ], [
            'ruangan_id.required' => 'Ruangan wajib dipilih.',
            'tanggal.required' => 'Tanggal peminjaman wajib diisi.',
            'tanggal.after_or_equal' => 'Tanggal peminjaman tidak boleh tanggal yang sudah lewat.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai peminjaman.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1 orang.',
            'alasan_peminjaman.required' => 'Alasan atau tujuan peminjaman wajib diisi.',
            'alasan_peminjaman.min' => 'Alasan peminjaman minimal harus 10 karakter.',
        ]);

        try {
            $peminjaman = $this->bookingService->createBooking($user, $validated);

            return redirect()->route('peminjaman.index')
                ->with('success', "Peminjaman ruangan berhasil diajukan dan disetujui otomatis! Kode Booking: {$peminjaman->kode_booking}");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Gagal memproses pengajuan peminjaman.', [
                'user_id' => $user->id,
                'exception' => $e,
            ]);

            return back()->with('error', 'Pengajuan belum dapat diproses. Silakan coba lagi atau hubungi pengelola.')->withInput();
        }
    }

    /**
     * Tampilkan riwayat dan status peminjaman milik pengguna yang sedang login.
     */
    public function index()
    {
        Peminjaman::autoExpirePeminjaman();

        $user = Auth::user();

        $peminjamanList = Peminjaman::with('ruangan')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('peminjaman.index', compact('peminjamanList'));
    }

    /**
     * Tampilkan detail peminjaman (hanya pemilik dan biro yang dapat melihat).
     */
    public function show(Peminjaman $peminjaman)
    {
        Peminjaman::autoExpirePeminjaman();

        $user = Auth::user();

        if ($peminjaman->user_id !== $user->id && !$user->isBiro()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki akses ke detail peminjaman ini.');
        }

        $peminjaman->load(['ruangan.fasilitas', 'user', 'pembuatPembatalan']);

        return view('peminjaman.show', compact('peminjaman'));
    }

    /**
     * Batalkan peminjaman oleh User (hanya sebelum jam mulai).
     */
    public function cancel(Peminjaman $peminjaman)
    {
        $user = Auth::user();

        try {
            $this->bookingService->cancelByUser($peminjaman, $user);
            return back()->with('success', "Peminjaman {$peminjaman->kode_booking} berhasil dibatalkan.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
