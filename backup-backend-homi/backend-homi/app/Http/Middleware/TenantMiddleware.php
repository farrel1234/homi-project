<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // LOG AWAL (Jangan panggil auth()->check() di sini! Karena DB belum di-switch!)
        Log::info("[INIT TENANCY] Incoming Request", [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'session_id' => session()->getId(),
        ]);

        // 1. JANGAN HIJACK jika di area login, logout, atau manajemen superadmin
        if ($request->is('/') || $request->is('admin/login*') || $request->is('login*') || $request->is('admin/logout*') || $request->is('admin/tenants*')) {
            return $next($request);
        }

        // 2. TENTUKAN TARGET DATABASE dari Session atau Cookie
        $tenantId = session()->get('impersonated_tenant_id') ?? session()->get('tenant_id') ?? $request->cookie('homi_tenant_id');

        if ($tenantId) {
            try {
                $tenant = $this->tenantService->findById($tenantId);

                if ($tenant) {
                    // Pindah jalur database ke tenant
                    $this->tenantService->switchToTenant($tenant);

                    // DEEP SYNC: Re-kalibrasi Auth Guard SETELAH database switch
                    Auth::shouldUse('web');
                    $guard = Auth::guard('web');
                    if (method_exists($guard, 'forgetUser')) {
                        $guard->forgetUser();
                    }

                    // Sinkronkan session jika tadi cuma ada di cookie
                    if (!session()->has('tenant_id')) {
                        session()->put('tenant_id', $tenant->id);
                        session()->put('tenant_name', $tenant->name);
                    }

                    Log::info("[TENANT DEBUG] Switched to tenant DB", [
                        'tenant_id' => $tenant->id,
                        'db' => $tenant->db_database,
                    ]);
                } else {
                    // Tenant tidak valid, bersihkan
                    session()->forget(['impersonated_tenant_id', 'tenant_id', 'tenant_name']);
                    cookie()->queue(cookie()->forget('homi_tenant_id'));
                }
            } catch (\Exception $e) {
                Log::error("[TENANT ERROR] Failed to switch to tenant: " . $e->getMessage());
                
                // Jika DB tidak ada atau koneksi gagal, paksa balik ke central
                session()->forget(['impersonated_tenant_id', 'tenant_id', 'tenant_name']);
                cookie()->queue(cookie()->forget('homi_tenant_id'));

                return redirect()->route('admin.dashboard')->with('error', 'Database perumahan bermasalah atau tidak ditemukan.');
            }
        }

        // 3. Cek Auth SETELAH database sudah benar (Bungkus Try-Catch juga)
        try {
            if (auth()->check()) {
                $user = auth()->user();

                Log::info("[MIDDLEWARE DEBUG] User Authenticated", [
                    'id'        => $user->id,
                    'email'     => $user->email,
                    'role'      => $user->role,
                    'db'        => config('database.connections.' . config('database.default') . '.database'),
                    'tenant_id' => $user->tenant_id ?? 'null'
                ]);

                // Kembalikan ke Pusat JIKA dia Super Admin Murni (dan tidak sedang impersonate)
                if ($user->isSuperAdmin() && empty($user->tenant_id) && !session()->has('impersonated_tenant_id')) {
                    Log::info("[MIDDLEWARE DEBUG] Global Super Admin - Staying on Central");
                    $manager = app(\App\Support\Tenancy\TenantManager::class);
                    $manager->deactivate();
                }
            }
        } catch (\Exception $e) {
            Log::error("[AUTH ERROR] Failed to check auth on tenant DB: " . $e->getMessage());
            
            // EMERGENCY RESET: Hapus jejak tenant di session & cookie
            session()->forget(['impersonated_tenant_id', 'tenant_id', 'tenant_name']);
            cookie()->queue(cookie()->forget('homi_tenant_id'));

            // Pastikan koneksi balik ke Central
            $manager = app(\App\Support\Tenancy\TenantManager::class);
            $manager->deactivate();

            return redirect()->route('admin.dashboard')->with('error', 'Sesi perumahan bermasalah. Anda telah dikembalikan ke pusat.');
        }

        return $next($request);
    }
}
