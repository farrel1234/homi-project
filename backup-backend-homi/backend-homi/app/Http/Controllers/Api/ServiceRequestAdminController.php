<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestPdfService;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $pdfGenerated = false;
        $warning = null;
        if ($data['status'] === 'approved') {
            try {
                $path = $pdfService->generate($sr);
                $sr->pdf_path = $path;
                $pdfGenerated = true;
            } catch (\Throwable $e) {
                $warning = 'Gagal generate PDF: ' . $e->getMessage();
            }
        }

        $sr->status = $data['status'];

        $adminNote = (string) ($data['admin_note'] ?? '');
        if ($warning) {
            $adminNote = trim($adminNote);
            $adminNote = $adminNote !== '' ? $adminNote . ' | ' . $warning : $warning;
        }
        $sr->admin_note = $adminNote !== '' ? $adminNote : null;

        $sr->verified_by = $request->user()->id;
        $sr->verified_at = now();
        $sr->save();

        // Send FCM Notification
        if ($sr->user && $sr->user->fcm_token) {
            $fcm = new FirebaseService();
            $statusLabel = match($sr->status) {
                'processed' => 'Sedang Diproses',
                'approved'  => 'Disetujui',
                'rejected'  => 'Ditolak',
                default     => strtoupper($sr->status)
            };
            
            $fcm->sendNotification(
                $sr->user->fcm_token,
                "Update Pengajuan: " . ($sr->type->name ?? 'Surat'),
                "Status pengajuan Anda kini: {$statusLabel}." . ($sr->status === 'approved' ? " PDF sudah dapat diunduh." : "")
            );
        }

        return response()->json([
            'message' => $data['status'] === 'approved'
                ? ($pdfGenerated ? 'Pengajuan disetujui dan PDF berhasil dibuat.' : 'Pengajuan disetujui, namun PDF belum dibuat.')
                : 'Status pengajuan diperbarui',
            'pdf_generated' => $pdfGenerated,
            'warning' => $warning,
            'data' => $sr->fresh(['type:id,name,letter_type_id', 'user:id,name,email', 'verifier:id,name']),
        ]);
    }
}
