<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
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
        // Jika ada tenant_id di session, pastikan koneksi DB mengarah ke sana
        if (session()->has('tenant_id')) {
            $tenantId = session()->get('tenant_id');
            $tenant = $this->tenantService->findById($tenantId);

            if ($tenant) {
                $this->tenantService->switchToTenant($tenant);
            } else {
                // Jika tenant tidak ditemukan lagi (misal dihapus), paksa logout
                session()->forget(['tenant_id', 'tenant_name']);
                return redirect()->route('admin.login')->withErrors(['email' => 'Tenant tidak valid atau telah dinonaktifkan.']);
            }
        }

        return $next($request);
    }
}
