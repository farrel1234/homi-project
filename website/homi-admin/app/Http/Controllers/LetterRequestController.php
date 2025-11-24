<?php

namespace App\Http\Controllers;

use App\Models\LetterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class LetterRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $q      = $request->query('q');

        $items = LetterRequest::with(['user', 'type'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->whereHas('user', function ($uq) use ($q) {
                        $uq->where('full_name', 'like', "%{$q}%")
                           ->orWhere('email', 'like', "%{$q}%")
                           ->orWhere('username', 'like', "%{$q}%");
                    })->orWhereHas('type', function ($tq) use ($q) {
                        $tq->where('name', 'like', "%{$q}%");
                    });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('letter_requests.index', compact('items', 'status', 'q'));
    }

    public function show(LetterRequest $letterRequest)
    {
        $letterRequest->load(['user', 'type']);

        // HTML surat yang sudah diisi variable (buat preview)
        $filledHtml = $letterRequest->renderHtml();

        return view('letter_requests.show', [
            'item'      => $letterRequest,
            'filledHtml'=> $filledHtml,
        ]);
    }

    // Update umum (kalau nanti mau dipakai)
    public function update(Request $request, LetterRequest $letterRequest)
    {
        $data = $request->validate([
            'status' => ['required', 'in:submitted,processed,approved,rejected'],
        ]);

        $letterRequest->update($data);

        return redirect()
            ->route('letter-requests.show', $letterRequest)
            ->with('ok', 'Status pengajuan surat berhasil diperbarui.');
    }

    public function approve(Request $request, LetterRequest $letterRequest)
    {
        // Generate PDF + set status approved
        $pdfPath = $this->generatePdf($letterRequest);

        $letterRequest->update([
            'status'   => 'approved',
            'pdf_path' => $pdfPath,
        ]);

        return redirect()
            ->route('letter-requests.show', $letterRequest)
            ->with('ok', 'Surat berhasil disetujui dan PDF telah dibuat.');
    }

    public function reject(Request $request, LetterRequest $letterRequest)
    {
        $letterRequest->update([
            'status' => 'rejected',
        ]);

        return redirect()
            ->route('letter-requests.show', $letterRequest)
            ->with('ok', 'Pengajuan surat telah ditolak.');
    }

    public function download(LetterRequest $letterRequest)
    {
        if (! $letterRequest->pdf_path || ! Storage::disk('public')->exists($letterRequest->pdf_path)) {
            return redirect()
                ->route('letter-requests.show', $letterRequest)
                ->with('error', 'File PDF belum tersedia.');
        }

        return Storage::disk('public')->download($letterRequest->pdf_path);
    }

    /**
     * Generate PDF dari template HTML dan simpan ke storage.
     */
    protected function generatePdf(LetterRequest $letterRequest): string
    {
        $letterRequest->loadMissing(['user', 'type']);

        $html = $letterRequest->renderHtml();

        // Bungkus sedikit styling dasar
        $htmlWrapper = view('letter_requests.pdf_base', [
            'html'  => $html,
            'item'  => $letterRequest,
        ])->render();

        $pdf = Pdf::loadHTML($htmlWrapper);

        $userName   = str_replace(' ', '_', $letterRequest->user->full_name ?? $letterRequest->user->username ?? 'warga');
        $typeSlug   = $letterRequest->type->slug ?? 'surat';
        $fileName   = $typeSlug . '-' . $userName . '-' . now()->format('YmdHis') . '.pdf';
        $path       = 'surat/' . date('Y/m') . '/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
