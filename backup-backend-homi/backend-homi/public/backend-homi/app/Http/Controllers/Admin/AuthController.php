<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            // kalau mau filter aktif:
            // 'is_active' => 1,
        ];

        // NOTE: jangan pakai remember (karena tabel users kamu tidak punya remember_token)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Optional: batasi hanya role admin yang bisa masuk web
            $user = Auth::user();
            $isAdmin = method_exists($user, 'isAdmin')
                ? $user->isAdmin()
                : (($user->role ?? null) === 'admin' || (int)($user->role_id ?? 0) === 1);

            if (! $isAdmin) {
                Auth::logout();

                return back()
                    ->withErrors(['email' => 'Akun ini bukan admin.'])
                    ->onlyInput('email');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
