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
        // Pastikan kita ada di konteks database PUSAT (Bukan hijack)
        $manager = app(\App\Support\Tenancy\TenantManager::class);
        $manager->deactivate();

        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $tenants = Tenant::where('is_active', true)->get();

        return view('auth.login', compact('tenants'));
    }

    public function login(Request $request, TenantManager $tenantManager, \App\Services\TenantService $tenantService)
    {
        // Pastikan kita reset ke pusat dulu sebelum validasi tenant
        $tenantManager->deactivate();

        // Validasi input
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string',
            'tenant_id' => 'required', // Bisa 0 (Pusat) atau ID Tenant
        ]);

        $tenantId = $request->tenant_id;
        $isCentral = (string)$tenantId === '0';

        if (! $isCentral) {
            // 1. Ambil data tenant (Pakai Service agar tidak kena Handshake error di MySQL 8.4)
            $tenant = $tenantService->findById($tenantId);

            if (! $tenant) {
                return back()->withErrors(['email' => 'Perumahan tidak ditemukan.'])->onlyInput('email');
            }

            // 2. Aktifkan koneksi ke perumahan tersebut agar Auth::attempt mencari USER di perumahan ini
            $tenantManager->activate($tenant);
        } else {
            // Pastikan pusat (homi) aktif jika login sistem global
            $tenantManager->deactivate();
        }

        $credentials = $request->only('email', 'password');

        // [AUTH DEBUG] JANGAN HAPUS: Untuk melihat rincian saat login mental
        $currentDB = config("database.connections." . config('database.default') . ".database");
        \Illuminate\Support\Facades\Log::info("[AUTH DEBUG] Attempting Login", [
            'database' => $currentDB,
            'email'    => $credentials['email'],
            'tenant'   => $tenantId,
        ]);

        // 3. Cek User di database yang sudah kita pilih tadi (Pusat atau Perumahan)
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek jika login di PUSAT tapi bukan SUPER ADMIN
            if ($isCentral && ! $user->isSuperAdmin()) {
                Auth::logout();
                $tenantManager->deactivate(); // Balik ke pusat
                return back()->withErrors(['email' => 'Hanya Super Admin yang bisa masuk ke Sistem Pusat.'])->onlyInput('email');
            }

            // Batal panggil regenerate() untuk stabilitas sesi di Windows/Local
            // $request->session()->regenerate();
            
            if (! $isCentral) {
                // Simpan metadata ke session
                $request->session()->put('tenant_id', $tenant->id);
                $request->session()->put('tenant_name', $tenant->name);

                // JURUS PAMUNGKAS: Simpan juga di Cookie (tahan banting di Windows/Laragon)
                cookie()->queue(cookie()->forever('homi_tenant_id', $tenant->id));
            } else {
                // Hapus cookie tenant jika login di PUSAT
                cookie()->queue(cookie()->forget('homi_tenant_id'));
                $request->session()->forget(['tenant_id', 'tenant_name']);
            }

            // Optional: batasi hanya role admin/superadmin
            if (! ($user->isAdmin() || $user->isSuperAdmin())) {
                Auth::logout();
                $tenantManager->deactivate();
                return back()->withErrors(['email' => 'Akun ini tidak memiliki akses administrator.'])->onlyInput('email');
            }

            // [AUTH DEBUG]
            $redirectUrl = route('admin.dashboard');
            \Illuminate\Support\Facades\Log::info("[AUTH DEBUG] Login SUCCESS", [
                'database' => config("database.connections." . config('database.default') . ".database"),
                'email'    => $user->email,
                'redirect_url' => $redirectUrl,
                'session_id' => session()->getId(),
                'session_tenant_id' => session()->get('tenant_id'),
                'auth_check' => Auth::check(),
                'auth_id' => Auth::id(),
            ]);

            return redirect($redirectUrl);
        }

        \Illuminate\Support\Facades\Log::warning("[AUTH DEBUG] Login FAILED", [
            'database' => config("database.connections." . config('database.default') . ".database"),
            'email'    => $request->email,
        ]);

            // 4. Jika gagal login, kembalikan konteks ke PUSAT agar form bisa dimuat lagi
            $tenantManager->deactivate();

            return back()
            ->withErrors(['email' => 'Email atau Password tidak cocok untuk perumahan yang dipilih.'])
            ->onlyInput('email');
    }

    public function logout(Request $request, TenantManager $tenantManager)
    {
        Auth::logout();
        $tenantManager->deactivate(); // Selalu balik ke pusat saat logout

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
