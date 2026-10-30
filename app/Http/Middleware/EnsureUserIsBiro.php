<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsBiro
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isBiro()) {
            abort(403, 'Akses Terlarang: Halaman ini hanya dapat diakses oleh Biro Pengelola.');
        }

        return $next($request);
    }
}
