<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Biro\JadwalTetapController;
use App\Http\Controllers\Biro\LaporanKerusakanBiroController;
use App\Http\Controllers\Biro\PeminjamanBiroController;
use App\Http\Controllers\Biro\RuanganController;
use App\Http\Controllers\Biro\UserVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanKerusakanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\RuanganCatalogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Peminjaman Ruangan & Lab Kampus
|--------------------------------------------------------------------------
*/

// Halaman Publik / Beranda
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

// Katalog Ruangan & Cek Ketersediaan (Publik & Terautentikasi)
Route::get('/ruangan', [RuanganCatalogController::class, 'index'])->name('ruangan.index');
Route::get('/ruangan/{ruangan}', [RuanganCatalogController::class, 'show'])->name('ruangan.show');

// Rute Tamu (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Terautentikasi
Route::middleware(['auth', 'active'])->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    // Alur Verifikasi Email
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Halaman Pemberitahuan Identitas Kampus Menunggu / Ditolak
    Route::get('/identitas/status', [AuthController::class, 'showIdentityNotice'])->name('verification.identity.notice');

    // Dashboard Pengguna (Dinamis: Biro vs User)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MODUL PEMINJAMAN RUANGAN (Mahasiswa & Dosen)
    // Pengajuan peminjaman membutuhkan verifikasi identitas resmi kampus
    Route::middleware('verified.identity')->group(function () {
        Route::get('/peminjaman/ajukan', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    });

    Route::get('/peminjaman-saya', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman-saya/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::patch('/peminjaman-saya/{peminjaman}/batal', [PeminjamanController::class, 'cancel'])->name('peminjaman.cancel');

    // MODUL PELAPORAN KERUSAKAN FASILITAS
    Route::get('/laporan-fasilitas', [LaporanKerusakanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan-fasilitas/buat', [LaporanKerusakanController::class, 'create'])->name('laporan.create');
    Route::post('/laporan-fasilitas', [LaporanKerusakanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan-fasilitas/{laporan}/foto', [LaporanKerusakanController::class, 'foto'])->name('laporan.foto');
    Route::get('/laporan-fasilitas/{laporan}', [LaporanKerusakanController::class, 'show'])->name('laporan.show');

    // MODUL NOTIFIKASI
    Route::get('/notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{notifikasi}/baca', [\App\Http\Controllers\NotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/tandai-semua-dibaca', [\App\Http\Controllers\NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.mark-all-read');

    // =========================================================================
    // AREA ADMINISTRATIF KHUSUS BIRO PENGELOLA (Dilindungi middleware 'biro')
    // =========================================================================
    Route::middleware('biro')->prefix('biro')->name('biro.')->group(function () {
        // 1. Verifikasi Akun Pengguna
        Route::get('/verifikasi-pengguna', [UserVerificationController::class, 'index'])->name('verifikasi.index');
        Route::patch('/verifikasi-pengguna/{user}/approve', [UserVerificationController::class, 'approve'])->name('verifikasi.approve');
        Route::patch('/verifikasi-pengguna/{user}/reject', [UserVerificationController::class, 'reject'])->name('verifikasi.reject');
        Route::patch('/verifikasi-pengguna/{user}/toggle-active', [UserVerificationController::class, 'toggleActive'])->name('verifikasi.toggle-active');

        // 2. Kelola Ruangan & Fasilitas
        Route::resource('ruangan', RuanganController::class)->except(['show', 'destroy']);
        Route::patch('/ruangan/{ruangan}/toggle-status', [RuanganController::class, 'toggleStatus'])->name('ruangan.toggle-status');

        // 3. Kelola Jadwal Tetap Kampus
        Route::resource('jadwal', JadwalTetapController::class)->except(['show', 'edit', 'update']);

        // 4. Monitoring Seluruh Peminjaman & Pembatalan Resmi
        Route::get('/peminjaman', [PeminjamanBiroController::class, 'index'])->name('peminjaman.index');
        Route::patch('/peminjaman/{peminjaman}/batal', [PeminjamanBiroController::class, 'cancel'])->name('peminjaman.cancel');

        // 5. Penanganan Laporan Kerusakan Fasilitas
        Route::get('/laporan', [LaporanKerusakanBiroController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/{laporan}', [LaporanKerusakanBiroController::class, 'show'])->name('laporan.show');
        Route::patch('/laporan/{laporan}/update-status', [LaporanKerusakanBiroController::class, 'updateStatus'])->name('laporan.update-status');
    });
});
