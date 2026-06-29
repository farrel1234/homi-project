<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class TenantController extends Controller
{
    /**
     * Get list of active tenants for mobile dropdown.
     */
    public function index(): JsonResponse
    {
        // Ambil tenant yang aktif saja
        $tenants = Tenant::where('is_active', true)
            ->select('name', 'code')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $tenants,
            'message' => 'Tenant list matching identification header'
        ]);
    }
}
