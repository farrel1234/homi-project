<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TenantRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantRequestController extends Controller
{
    /**
     * Simpan permintaan trial dari landing page.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'manager_name' => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:50',
            'notes'        => 'nullable|string',
        ]);

        $tenantRequest = TenantRequest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan trial berhasil dikirim. Tim kami akan menghubungi Anda segera.',
            'data'    => $tenantRequest,
        ], 201);
    }
}
