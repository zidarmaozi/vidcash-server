<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

       // -- LOGIKA BARU DIMULAI DARI SINI --

    // Ambil role dari user yang sedang login
    $role = Auth::user()->role;

    // Cek role dan arahkan ke halaman yang sesuai
    if ($role == 'admin') {
        return redirect()->intended('/admin');
    }

    // Jika bukan admin, arahkan ke dashboard biasa
    return redirect()->intended('/dashboard');
  }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
