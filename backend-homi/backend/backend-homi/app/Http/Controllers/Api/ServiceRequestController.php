<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RequestType;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceRequestController extends Controller
{
    // Dropdown jenis pengajuan
    public function types(): JsonResponse
    {
        return response()->json([
            'data' => RequestType::where('is_active', true)->get(['id', 'name'])
        ]);
    }

    // Warga membuat pengajuan
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'reporter_name'   => 'required|string|max:255',
            'request_type_id' => 'required|exists:request_types,id',
            'request_date'    => 'required|date',
            'place'           => 'required|string|max:255',
            'subject'         => 'required|string|max:255',
        ]);

        $requestData = ServiceRequest::create([
            ...$data,
            'user_id' => $request->user()->id,
            'status'  => 'submitted',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil dikirim',
            'data'    => $requestData,
        ], 201);
    }

    // Riwayat pengajuan milik warga
    public function index(Request $request): JsonResponse
    {
        $data = ServiceRequest::with('type:id,name')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['data' => $data]);
    }

    // Detail pengajuan milik warga
    public function show(Request $request, int $id): JsonResponse
    {
        $data = ServiceRequest::with(['type:id,name', 'verifier:id,name'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json(['data' => $data]);
    }
}
