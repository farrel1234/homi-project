<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $q = $request->query('q');

        $items = ServiceRequest::with(['user', 'type'])
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('subject', 'like', "%{$q}%")
                        ->orWhere('place', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($uq) use ($q) {
                            $uq->where('name', 'like', "%{$q}%")
                               ->orWhere('full_name', 'like', "%{$q}%")
                               ->orWhere('email', 'like', "%{$q}%")
                               ->orWhere('username', 'like', "%{$q}%");
                        })
                        ->orWhereHas('type', function ($tq) use ($q) {
                            $tq->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('service_requests.index', compact('items', 'status', 'q'));
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['user', 'type', 'verifier']);

        // data_input kadang null / string json / array
        $dataInput = $serviceRequest->data_input;
        if (is_string($dataInput)) {
            $decoded = json_decode($dataInput, true);
            $dataInput = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        return view('service_requests.show', [
            'item' => $serviceRequest,
            'dataInput' => is_array($dataInput) ? $dataInput : [],
        ]);
    }

    public function approve(ServiceRequest $serviceRequest, ServiceRequestPdfService $pdfService)
    {
        // generate PDF hanya kalau request_type terhubung ke letter_types
        $serviceRequest->loadMissing(['type.letterType', 'user.residentProfile']);

        if (!$serviceRequest->type?->letterType) {
            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('error', 'Jenis pengajuan ini belum terhubung ke template surat (request_types.letter_type_id masih NULL).');
        }

        try {
            $path = $pdfService->generate($serviceRequest);
            $serviceRequest->pdf_path = $path;
        } catch (\Throwable $e) {
            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }

        $serviceRequest->status = 'approved';
        $serviceRequest->admin_note = request('admin_note');
        $serviceRequest->verified_by = auth()->id();
        $serviceRequest->verified_at = now();
        $serviceRequest->save();

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('ok', 'Pengajuan disetujui & PDF berhasil dibuat.');
    }

    public function reject(ServiceRequest $serviceRequest)
    {
        $serviceRequest->status = 'rejected';
        $serviceRequest->admin_note = request('admin_note');
        $serviceRequest->verified_by = auth()->id();
        $serviceRequest->verified_at = now();
        $serviceRequest->save();

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('ok', 'Pengajuan ditolak.');
    }

    public function download(ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->pdf_path || !Storage::disk('public')->exists($serviceRequest->pdf_path)) {
            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('error', 'PDF belum tersedia / file tidak ditemukan.');
        }

        return Storage::disk('public')->download($serviceRequest->pdf_path);
    }
}
