<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\AppNotification;
use App\Services\FirebaseService;
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

    /**
     * Preview PDF di browser (tanpa approve / simpan).
     */
    public function preview(ServiceRequest $serviceRequest, ServiceRequestPdfService $pdfService)
    {
        try {
            $html = $pdfService->renderHtml($serviceRequest);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('A4', 'portrait');
            return $pdf->stream('preview-pengajuan-' . $serviceRequest->id . '.pdf');
        } catch (\Throwable $e) {
            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('error', 'Gagal membuat preview PDF: ' . $e->getMessage());
        }
    }

    public function approve(ServiceRequest $serviceRequest, ServiceRequestPdfService $pdfService, FirebaseService $firebaseService)
    {
        $serviceRequest->loadMissing(['type.letterType', 'user.residentProfile']);

        // Tangkap input baru dari admin
        $data = $serviceRequest->data_input;
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $data = is_array($decoded) ? $decoded : [];
        }
        $data = is_array($data) ? $data : [];

        if (request()->has('rt')) $data['rt'] = request('rt');
        if (request()->has('rw')) $data['rw'] = request('rw');
        if (request()->has('nama_rt')) $data['nama_rt'] = request('nama_rt');
        if (request()->has('nik')) $data['nik'] = request('nik');
        if (request()->has('tmpt_tgl_lahir')) $data['tmpt_tgl_lahir'] = request('tmpt_tgl_lahir');
        if (request()->has('pekerjaan')) $data['pekerjaan'] = request('pekerjaan');
        if (request()->has('alamat')) $data['alamat'] = request('alamat');
        
        $serviceRequest->data_input = $data;

        if (request()->has('subject') && !empty(request('subject'))) {
            $serviceRequest->subject = request('subject');
        }

        $pdfGenerated = false;
        $warning = null;
        try {
            $path = $pdfService->generate($serviceRequest);
            $serviceRequest->pdf_path = $path;
            $pdfGenerated = true;
        } catch (\Throwable $e) {
            $warning = 'PDF belum berhasil digenerate: ' . $e->getMessage();
        }

        $serviceRequest->status = 'approved';
        $adminNote = (string) request('admin_note', '');
        if ($warning) {
            $adminNote = trim($adminNote);
            $adminNote = $adminNote !== '' ? $adminNote . ' | ' . $warning : $warning;
        }
        $serviceRequest->admin_note = $adminNote !== '' ? $adminNote : null;
        $serviceRequest->verified_by = auth()->id();
        $serviceRequest->verified_at = now();
        $serviceRequest->save();

        // Send Notification
        $title = "Pengajuan Disetujui";
        $msg = "Pengajuan " . ($serviceRequest->type->name ?? 'Layanan') . " Anda telah disetujui.";
        AppNotification::create([
            'user_id' => $serviceRequest->user_id,
            'sent_by' => auth()->id(),
            'title'   => $title,
            'message' => $msg,
            'type'    => 'service_request_approved',
            'data'    => ['id' => $serviceRequest->id, 'route' => 'DetailLayanan']
        ]);

        if ($serviceRequest->user && $serviceRequest->user->fcm_token) {
            $firebaseService->sendNotification(
                $serviceRequest->user->fcm_token,
                $title,
                $msg,
                ['route' => 'DetailLayanan', 'id' => (string)$serviceRequest->id]
            );
        }

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('ok', $pdfGenerated
                ? 'Pengajuan disetujui dan PDF berhasil dibuat.'
                : 'Pengajuan disetujui, tetapi PDF belum berhasil dibuat.');
    }

    public function reject(ServiceRequest $serviceRequest, FirebaseService $firebaseService)
    {
        $serviceRequest->status = 'rejected';
        $serviceRequest->admin_note = request('admin_note');
        $serviceRequest->verified_by = auth()->id();
        $serviceRequest->verified_at = now();
        $serviceRequest->save();

        // Send Notification
        $title = "Pengajuan Ditolak";
        $msg = "Mohon maaf, pengajuan " . ($serviceRequest->type->name ?? 'Layanan') . " Anda ditolak. Catatan: " . ($serviceRequest->admin_note ?? '-');
        AppNotification::create([
            'user_id' => $serviceRequest->user_id,
            'sent_by' => auth()->id(),
            'title'   => $title,
            'message' => $msg,
            'type'    => 'service_request_rejected',
            'data'    => ['id' => $serviceRequest->id, 'route' => 'DetailLayanan']
        ]);

        if ($serviceRequest->user && $serviceRequest->user->fcm_token) {
            $firebaseService->sendNotification(
                $serviceRequest->user->fcm_token,
                $title,
                $msg,
                ['route' => 'DetailLayanan', 'id' => (string)$serviceRequest->id]
            );
        }

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('ok', 'Pengajuan ditolak.');
    }

    public function download(ServiceRequest $serviceRequest, ServiceRequestPdfService $pdfService)
    {
        $resolvedPath = $pdfService->resolveExistingPublicPath($serviceRequest->pdf_path);

        // fallback untuk data lama: jika file hilang tetapi sudah approved, coba generate ulang
        if (!$resolvedPath && $serviceRequest->status === 'approved') {
            try {
                $serviceRequest->loadMissing(['type.letterType', 'user.residentProfile']);
                $resolvedPath = $pdfService->generate($serviceRequest);
                $serviceRequest->pdf_path = $resolvedPath;
                $serviceRequest->save();
            } catch (\Throwable $e) {
                $resolvedPath = null;
            }
        }

        if (!$resolvedPath) {
            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('error', 'PDF belum tersedia / file tidak ditemukan.');
        }

        return Storage::disk('public')->download($resolvedPath);
    }
}
