<?php

namespace App\Http\Controllers;

use App\Models\JadwalTetap;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard sesuai role (Biro atau User).
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isBiro()) {
            return $this->biroDashboard();
        }

        return $this->userDashboard();
    }

    /**
     * Dashboard khusus untuk Biro Pengelola.
     */
    protected function biroDashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'users_menunggu_verifikasi' => User::where('role', 'user')->where('status_verifikasi', 'menunggu')->count(),
            'users_terverifikasi' => User::where('role', 'user')->where('status_verifikasi', 'terverifikasi')->count(),
            'total_ruangan' => Ruangan::count(),
            'total_lab' => Ruangan::where('jenis_ruangan', 'laboratorium')->count(),
            'total_lantai' => Ruangan::query()->distinct()->count('lantai'),
            'ruangan_perawatan' => Ruangan::where('status_operasional', 'perawatan')->count(),
            'peminjaman_hari_ini' => Peminjaman::whereDate('tanggal', today())->where('status', 'disetujui')->count(),
            'peminjaman_aktif' => Peminjaman::where('status', 'disetujui')->count(),
            'laporan_pending' => LaporanKerusakan::whereIn('status', ['menunggu', 'sedang_ditangani'])->count(),
        ];

        $pendingUsers = User::where('role', 'user')
            ->where('status_verifikasi', 'menunggu')
            ->latest()
            ->take(5)
            ->get();

        $todayBookings = Peminjaman::with(['user', 'ruangan'])
            ->whereDate('tanggal', today())
            ->where('status', 'disetujui')
            ->orderBy('jam_mulai')
            ->take(6)
            ->get();

        $recentReports = LaporanKerusakan::with(['user', 'ruangan'])
            ->whereIn('status', ['menunggu', 'sedang_ditangani'])
            ->latest()
            ->take(5)
            ->get();

        return view('biro.dashboard', compact('stats', 'pendingUsers', 'todayBookings', 'recentReports'));
    }

    /**
     * Dashboard khusus untuk Pengguna (Mahasiswa / Dosen).
     */
    protected function userDashboard()
    {
        $user = Auth::user();

        // 1 Peminjaman Aktif Milik Pengguna
        $activeBooking = Peminjaman::with('ruangan')
            ->where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where(function ($q) {
                $nowDate = now()->toDateString();
                $nowTime = now()->toTimeString();
                $q->where('tanggal', '>', $nowDate)
                  ->orWhere(function ($sub) use ($nowDate, $nowTime) {
                      $sub->where('tanggal', $nowDate)
                          ->where('jam_selesai', '>', $nowTime);
                  });
            })
            ->first();

        $recentBookings = Peminjaman::with('ruangan')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentReports = LaporanKerusakan::with('ruangan')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('user', 'activeBooking', 'recentBookings', 'recentReports'));
    }
}
