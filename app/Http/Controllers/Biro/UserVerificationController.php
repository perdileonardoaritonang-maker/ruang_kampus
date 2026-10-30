<?php

namespace App\Http\Controllers\Biro;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVerificationController extends Controller
{
    /**
     * Tampilkan daftar akun pengguna yang membutuhkan verifikasi atau sudah diverifikasi.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'menunggu');
        $search = $request->query('q');

        $query = User::where('role', 'user');

        if (in_array($status, ['menunggu', 'terverifikasi', 'ditolak'])) {
            $query->where('status_verifikasi', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_identitas', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->with('verifikator')->latest()->paginate(15)->withQueryString();

        $counts = [
            'menunggu' => User::where('role', 'user')->where('status_verifikasi', 'menunggu')->count(),
            'terverifikasi' => User::where('role', 'user')->where('status_verifikasi', 'terverifikasi')->count(),
            'ditolak' => User::where('role', 'user')->where('status_verifikasi', 'ditolak')->count(),
        ];

        return view('biro.verifikasi.index', compact('users', 'status', 'search', 'counts'));
    }

    /**
     * Setujui verifikasi identitas kampus pengguna.
     */
    public function approve(User $user)
    {
        // Proteksi: jangan memverifikasi sesama biro
        if ($user->isBiro()) {
            return back()->with('error', 'Tindakan tidak valid.');
        }

        $user->status_verifikasi = 'terverifikasi';
        $user->alasan_penolakan = null;
        $user->diverifikasi_oleh = Auth::id();
        $user->diverifikasi_pada = now();
        $user->save();

        // Kirim Notifikasi ke User
        \App\Models\Notifikasi::kirimKeUser(
            $user->id,
            'Akun Anda Telah Diverifikasi',
            "Selamat! Berkas identitas Anda ({$user->nomor_identitas}) telah disetujui oleh Biro. Anda kini dapat melakukan peminjaman ruangan kuliah dan laboratorium.",
            'akun_diverifikasi',
            route('ruangan.index')
        );

        return back()->with('success', "Identitas pengguna {$user->nama_lengkap} ({$user->nomor_identitas}) berhasil disetujui.");
    }

    /**
     * Tolak verifikasi identitas kampus pengguna disertai alasan.
     */
    public function reject(Request $request, User $user)
    {
        if ($user->isBiro()) {
            return back()->with('error', 'Tindakan tidak valid.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan verifikasi wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $user->status_verifikasi = 'ditolak';
        $user->alasan_penolakan = trim($validated['alasan_penolakan']);
        $user->diverifikasi_oleh = Auth::id();
        $user->diverifikasi_pada = now();
        $user->save();

        // Kirim Notifikasi ke User
        \App\Models\Notifikasi::kirimKeUser(
            $user->id,
            'Verifikasi Identitas Ditolak',
            "Permohonan verifikasi identitas Anda ({$user->nomor_identitas}) ditolak oleh Biro dengan catatan: '{$user->alasan_penolakan}'. Silakan perbaiki data identitas Anda.",
            'akun_diverifikasi',
            route('dashboard')
        );

        return back()->with('success', "Permohonan verifikasi identitas {$user->nama_lengkap} telah ditolak dengan catatan yang tersimpan.");
    }

    /**
     * Toggle status aktif/nonaktif akun pengguna.
     */
    public function toggleActive(User $user)
    {
        if ($user->isBiro()) {
            return back()->with('error', 'Tidak dapat mengubah status akun Biro sesama pengelola.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->nama_lengkap} berhasil {$statusText}.");
    }
}
