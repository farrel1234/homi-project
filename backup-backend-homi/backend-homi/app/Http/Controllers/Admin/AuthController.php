<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use App\Support\Tenancy\TenantManager;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $tenants = Tenant::where('is_active', true)->get();

        return view('auth.login', compact('tenants'));
    }

    public function login(Request $request, TenantManager $tenantManager)
    {
        // Validasi input
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string',
            'tenant_id' => 'required', // Bisa 0 (Pusat) atau ID Tenant
        ]);

        $tenantId = $request->tenant_id;
        $isCentral = (string)$tenantId === '0';

        if (! $isCentral) {
            // 1. Ambil data tenant dari Central DB
            $tenant = Tenant::on('central')->findOrFail($tenantId);

            // 2. Aktifkan koneksi ke tenant tersebut
            $tenantManager->activate($tenant);
        }

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        // NOTE: jangan pakai remember
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek jika login di PUSAT tapi bukan SUPER ADMIN
            if ($isCentral && ! $user->isSuperAdmin()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Hanya Super Admin yang bisa masuk ke Sistem Pusat.'])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            if (! $isCentral) {
                // 3. Simpan tenant_id di session agar middleware bisa meneruskan koneksi
                $request->session()->put('tenant_id', $tenant->id);
                $request->session()->put('tenant_name', $tenant->name);
            } else {
                $request->session()->put('tenant_name', 'Sistem Pusat (Homi Global)');
            }

            // Optional: batasi hanya role admin yang bisa masuk web
            $isAdmin = $user->isAdmin() || $user->isSuperAdmin();

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
