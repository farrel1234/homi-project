<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestPdfService;
use Illuminate\Http\Request;

class ServiceRequestAdminController extends Controller
{
    public function updateStatus(Request $request, int $id, ServiceRequestPdfService $pdfService)
    {
        $data = $request->validate([
            'status'     => 'required|in:processed,approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $sr = ServiceRequest::with(['user.residentProfile', 'type.letterType'])->findOrFail($id);

        // update status basic
        $sr->status      = $data['status'];
        $sr->admin_note  = $data['admin_note'] ?? null;
        $sr->verified_by = $request->user()->id;
        $sr->verified_at = now();

        // kalau approve → generate PDF
        if ($data['status'] === 'approved') {
            try {
                $pdfPath = $pdfService->generateFromServiceRequest($sr);
                $sr->pdf_path = $pdfPath;
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Gagal generate PDF: ' . $e->getMessage(),
                ], 422);
            }
        }

        $sr->save();

        return response()->json([
            'message' => 'Status pengajuan diperbarui',
            'data'    => $sr->fresh(['user.residentProfile', 'type.letterType']),
        ]);
    }
}
