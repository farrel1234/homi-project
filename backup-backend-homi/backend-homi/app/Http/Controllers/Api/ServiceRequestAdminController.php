<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestPdfService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceRequestAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');

        $data = ServiceRequest::with(['type:id,name,letter_type_id', 'user:id,name,email'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->get();

        return response()->json(['data' => $data]);
    }

    public function show(int $id): JsonResponse
    {
        $data = ServiceRequest::with(['type:id,name,letter_type_id', 'user:id,name,email', 'verifier:id,name'])
            ->findOrFail($id);

        return response()->json(['data' => $data]);
    }

    public function updateStatus(Request $request, int $id, ServiceRequestPdfService $pdfService): JsonResponse
    {
        $data = $request->validate([
            'status'     => 'required|in:processed,approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $sr = ServiceRequest::with(['type.letterType', 'user.residentProfile'])->findOrFail($id);

        // kalau approve → wajib ada template surat, dan wajib generate pdf
        if ($data['status'] === 'approved') {
            if (!$sr->type?->letterType) {
                return response()->json([
                    'message' => 'Gagal approve: jenis pengajuan ini belum terhubung ke template surat (request_types.letter_type_id).'
                ], 422);
            }

            try {
                $path = $pdfService->generate($sr);
                $sr->pdf_path = $path;
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Gagal generate PDF: ' . $e->getMessage()
                ], 422);
            }
        }

        $sr->status      = $data['status'];
        $sr->admin_note  = $data['admin_note'] ?? null;
        $sr->verified_by = $request->user()->id;
        $sr->verified_at = now();
        $sr->save();

        return response()->json([
            'message' => 'Status pengajuan diperbarui',
            'data'    => $sr->fresh(['type:id,name,letter_type_id', 'user:id,name,email', 'verifier:id,name']),
        ]);
    }
}
