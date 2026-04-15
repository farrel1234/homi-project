<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $feature
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        // 1. Ambil tenant dari request attributes (set oleh ResolveTenant)
        $tenant = $request->attributes->get('tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Konteks perumahan (tenant) tidak ditemukan.',
            ], 403);
        }

        // 2. Cek fitur lewat model Tenant
        if (!$tenant->hasFeature($feature)) {
            $planName = config("plans.plans.{$tenant->plan}.name", $tenant->plan);
            
            return response()->json([
                'success' => false,
                'message' => "Fitur '{$feature}' tidak tersedia di paket {$planName}. Silakan upgrade paket Anda.",
                'error_code' => 'FEATURE_LOCKED'
            ], 403);
        }

        return $next($request);
    }
}
