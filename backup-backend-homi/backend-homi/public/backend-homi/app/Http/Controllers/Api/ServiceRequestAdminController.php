<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceRequestAdminController extends Controller
{
    // Admin lihat semua pengajuan
    public function index(): JsonResponse
    {
        $data = ServiceRequest::with([
                'type:id,name',
                'user:id,name,email',
            ])
            ->latest()
            ->get();

        return response()->json(['data' => $data]);
    }

    // Admin lihat detail
    public function show(int $id): JsonResponse
    {
        $data = ServiceRequest::with([
                'type:id,name',
                'user:id,name,email',
                'verifier:id,name',
            ])
            ->findOrFail($id);

        return response()->json(['data' => $data]);
    }

    // Admin update status (verifikasi)
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status'     => 'required|in:processed,approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($id);

        $serviceRequest->update([
            'status'      => $data['status'],
            'admin_note'  => $data['admin_note'] ?? null,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'Status pengajuan diperbarui',
            'data'    => $serviceRequest,
        ]);
    }
}
