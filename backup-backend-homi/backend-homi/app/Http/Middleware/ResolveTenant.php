<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\Tenancy\TenantManager;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(private readonly TenantManager $tenantManager)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isExemptPath($request)) {
            return $next($request);
        }

        $tenantCode = $this->resolveTenantCode($request);

        if (! $tenantCode) {
            $tenantCode = trim((string) config('tenancy.fallback_tenant_code', ''));
        }

        if (! $tenantCode && ! (bool) config('tenancy.required', true)) {
            return $next($request);
        }

        if (! $tenantCode) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant code wajib diisi.',
                'errors' => [
                    'tenant_code' => ['Kirim tenant code lewat header X-Tenant-Code atau field tenant_code.'],
                ],
            ], 422);
        }

        try {
            $tenantCodeSearch = trim((string) $tenantCode);
            $tenant = Tenant::query()
                ->where(function ($q) use ($tenantCodeSearch) {
                    $q->where('code', $tenantCodeSearch)
                      ->orWhere('registration_code', $tenantCodeSearch);
                })
                ->where('is_active', true)
                ->first();
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant registry belum siap. Jalankan migrasi central DB terlebih dahulu.',
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }

        if (! $tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan atau nonaktif.',
                'errors' => [
                    'tenant_code' => [$tenantCode],
                ],
            ], 404);
        }

        $this->tenantManager->activate($tenant);
        $request->attributes->set('tenant', $tenant);

        // Security Patch: Reset Auth User after DB switch
        // This ensures auth()->user() fetches from the NEW database
        if (\Illuminate\Support\Facades\Auth::hasUser()) {
            \Illuminate\Support\Facades\Auth::forgetUser();
        }

        return $next($request);
    }

    private function resolveTenantCode(Request $request): ?string
    {
        // Prioritas ke-1: Payload (Form / JSON) - Ini paling spesifik (untuk registrasi dsb)
        foreach ((array) config('tenancy.payload_keys', []) as $payloadKey) {
            $value = trim((string) $request->input($payloadKey, ''));
            if ($value !== '') return $value;
        }

        // Prioritas ke-2: Header (X-Tenant-Code dsb) - Untuk sesi yang sudah berjalan
        foreach ((array) config('tenancy.header_keys', []) as $headerKey) {
            $value = trim((string) $request->header($headerKey, ''));
            if ($value !== '') return $value;
        }

        if (! (bool) config('tenancy.lookup_by_domain', true)) {
            return null;
        }

        $host = trim((string) $request->getHost());
        if ($host === '') {
            return null;
        }

        try {
            $tenant = Tenant::query()
                ->where('domain', $host)
                ->where('is_active', true)
                ->first();
        } catch (QueryException) {
            return null;
        }

        return $tenant?->code;
    }

    private function isExemptPath(Request $request): bool
    {
        foreach ((array) config('tenancy.exempt_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
