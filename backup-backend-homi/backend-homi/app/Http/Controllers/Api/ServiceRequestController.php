<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RequestType;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestPdfService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ServiceRequestController extends Controller
{
    public function types(): JsonResponse
    {
        return response()->json([
            'data' => RequestType::where('is_active', true)->get(['id', 'name', 'letter_type_id'])
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'reporter_name'   => 'required|string|max:255',
            'request_type_id' => 'required|exists:request_types,id',
            'request_date'    => 'required|date',
            'place'           => 'required|string|max:255',
            'subject'         => 'required|string|max:255',

            // optional sesuai tabel kamu
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',

            // ini kunci buat template surat
            'data_input'  => 'nullable|array',
        ]);

        $sr = ServiceRequest::create([
            'user_id'         => $request->user()->id,
            'reporter_name'   => $data['reporter_name'],
            'request_type_id' => $data['request_type_id'],
            'request_date'    => $data['request_date'],
            'place'           => $data['place'],
            'subject'         => $data['subject'],
            'title'           => $data['title'] ?? $data['subject'],
            'description'     => $data['description'] ?? null,
            'category'        => $data['category'] ?? null,
            'data_input'      => $data['data_input'] ?? null,
            'status'          => 'submitted',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil dikirim',
            'data'    => $sr->load('type:id,name'),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $data = ServiceRequest::with('type:id,name')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['data' => $data]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $data = ServiceRequest::with(['type:id,name', 'verifier:id,name'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json(['data' => $data]);
    }

    public function downloadPdf(Request $request, int $id, ServiceRequestPdfService $pdfService)
    {
        $sr = ServiceRequest::findOrFail($id);

        $user = $request->user();
        $isAdmin = ((int)($user->role_id ?? 0) === 1)
            || in_array(strtolower((string)($user->role ?? '')), ['admin','superadmin'], true);

        if (!$isAdmin && (int)$sr->user_id !== (int)$user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $resolvedPath = $pdfService->resolveExistingPublicPath($sr->pdf_path);

        if (!$resolvedPath && $sr->status === 'approved') {
            try {
                $sr->loadMissing(['type.letterType', 'user.residentProfile']);
                $resolvedPath = $pdfService->generate($sr);
                $sr->pdf_path = $resolvedPath;
                $sr->save();
            } catch (\Throwable $e) {
                $resolvedPath = null;
            }
        }

        if (!$resolvedPath) {
            return response()->json(['message' => 'PDF belum tersedia'], 404);
        }

        return Storage::disk('public')->download($resolvedPath);
    }
}
