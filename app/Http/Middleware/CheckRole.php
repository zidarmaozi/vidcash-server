<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika pengguna tidak login atau tidak punya role yang diizinkan
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            // Tampilkan halaman error "Tidak diizinkan"
            // abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
            switch (Auth::user()->role) {
                case 'user':
                    return redirect()->route('dashboard');
                case 'admin':
                    return redirect()->to('/admin');
                default:
                    abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
            }
        }

        // Jika role cocok, izinkan masuk
        return $next($request);
    }
}