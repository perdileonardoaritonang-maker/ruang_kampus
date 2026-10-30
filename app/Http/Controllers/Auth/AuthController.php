<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = 'login-attempt:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda dinonaktifkan oleh administrator kampus. Silakan hubungi bagian Biro.',
                ]);
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . $user->nama_lengkap . '!');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang dimasukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan formulir pendaftaran.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun baru.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'jenis_pengguna' => ['required', 'in:mahasiswa,dosen'],
            'nomor_identitas' => [
                'required',
                'string',
                'max:50',
                'unique:users,nomor_identitas',
                'alpha_dash',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jenis_pengguna.required' => 'Jenis pengguna wajib dipilih.',
            'jenis_pengguna.in' => 'Pilihan jenis pengguna harus Mahasiswa atau Dosen.',
            'nomor_identitas.required' => 'Nomor identitas kampus (NPM / NIDN) wajib diisi.',
            'nomor_identitas.unique' => 'Nomor identitas tersebut sudah terdaftar pada sistem.',
            'nomor_identitas.alpha_dash' => 'Nomor identitas hanya boleh berisi huruf, angka, tanda strip, atau garis bawah.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus terdiri dari 8 karakter.',
        ]);

        // Buat akun baru secara aman (role dan status verifikasi diatur eksplisit secara aman)
        $user = new User();
        $user->nama_lengkap = trim($validated['nama_lengkap']);
        $user->email = strtolower(trim($validated['email']));
        $user->jenis_pengguna = $validated['jenis_pengguna'];
        $user->nomor_identitas = strtoupper(trim($validated['nomor_identitas']));
        $user->password = Hash::make($validated['password']);
        $user->role = 'user'; // Terkunci aman sebagai User (Biro dibuat melalui Seeder/Admin)
        $user->status_verifikasi = 'menunggu';
        $user->is_active = true;
        $user->email_verified_at = now(); // Email otomatis tervalidasi tanpa perlu verifikasi tautan email
        $user->save();

        // Kirim notifikasi ke Biro tentang pendaftaran pengguna baru yang menunggu verifikasi berkas
        \App\Models\Notifikasi::kirimKeBiro(
            'Pengguna Baru Menunggu Verifikasi',
            "Pengguna baru {$user->nama_lengkap} ({$user->nomor_identitas}, " . ucfirst($user->jenis_pengguna) . ") telah mendaftar dan membutuhkan verifikasi berkas identitas.",
            'verifikasi_user',
            route('biro.verifikasi.index')
        );

        Auth::login($user);

        return redirect()->route('verification.identity.notice')
            ->with('success', 'Pendaftaran berhasil! Akun Anda telah terdaftar dan saat ini sedang menunggu verifikasi identitas dari Biro Kampus.');
    }

    /**
     * Tampilkan pemberitahuan verifikasi email.
     */
    public function showVerificationNotice()
    {
        return view('auth.verify-email');
    }

    /**
     * Kirim ulang tautan verifikasi email.
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Tautan verifikasi email baru telah dikirimkan ke alamat email Anda.');
    }

    /**
     * Verifikasi email via signed URL.
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('dashboard')
            ->with('success', 'Email berhasil diverifikasi! Selanjutnya Biro akan memeriksa identitas kampus Anda.');
    }

    /**
     * Tampilkan halaman pemberitahuan bahwa identitas kampus belum disetujui Biro.
     */
    public function showIdentityNotice()
    {
        $user = Auth::user();
        return view('auth.identity-notice', compact('user'));
    }

    /**
     * Proses logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
