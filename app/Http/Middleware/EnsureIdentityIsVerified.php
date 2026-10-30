<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdentityIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda dinonaktifkan. Silakan hubungi Biro Kampus.']);
        }

        // Jika user adalah Biro, izinkan lewat
        if ($user->isBiro()) {
            return $next($request);
        }

        // Cek status verifikasi identitas
        if (!$user->isIdentityVerified()) {
            return redirect()->route('verification.identity.notice')
                ->with('warning', 'Identitas kampus Anda belum diverifikasi oleh Biro atau permohonan ditolak. Anda belum dapat melakukan peminjaman ruangan.');
        }

        return $next($request);
    }
}
