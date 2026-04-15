<?php

// File: app/Http/Controllers/Admin/LetterRequestController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterRequest;
use App\Models\LetterType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class LetterRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $q      = $request->query('q');

        $items = LetterRequest::with(['user', 'type'])
            ->when($status, fn ($query) => $query->where('status', $status))
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

        $filledHtml = $letterRequest->renderHtml();

        return view('letter_requests.show', [
            'item'       => $letterRequest,
            'filledHtml' => $filledHtml,
        ]);
    }

    public function update(Request $request, LetterRequest $letterRequest)
    {
        // Terima 2 variasi biar aman kalau DB sudah terlanjur pakai "processed"
        $data = $request->validate([
            'status' => ['required', 'in:submitted,processing,processed,approved,rejected'],
        ]);

        $letterRequest->update($data);

        return redirect()
            ->route('letter-requests.show', $letterRequest)
            ->with('ok', 'Status pengajuan surat berhasil diperbarui.');
    }

    public function approve(Request $request, LetterRequest $letterRequest)
    {
        // Update data_input jika ada input baru dari form
        $data = is_array($letterRequest->data_input) ? $letterRequest->data_input : [];
        if ($request->has('nomor_surat') && !empty($request->nomor_surat)) $data['nomor_surat'] = $request->nomor_surat;
        if ($request->has('rt')) $data['rt'] = $request->rt;
        if ($request->has('rw')) $data['rw'] = $request->rw;
        if ($request->has('nama_rt')) $data['nama_rt'] = $request->nama_rt;

        $letterRequest->data_input = $data;
        $letterRequest->save();

        $pdfPath = $this->generatePdf($letterRequest);

        $letterRequest->update([
            'status'      => 'approved',
            'pdf_path'    => $pdfPath,
            'approved_at' => now(),
            'rejected_at' => null,
        ]);

        return redirect()
            ->route('letter-requests.show', $letterRequest)
            ->with('ok', 'Surat berhasil disetujui dan PDF telah dibuat.');
    }

    public function reject(Request $request, LetterRequest $letterRequest)
    {
        $letterRequest->update([
            'status'      => 'rejected',
            'rejected_at' => now(),
        ]);

        return redirect()
            ->route('letter-requests.show', $letterRequest)
            ->with('ok', 'Pengajuan surat telah ditolak.');
    }

    public function download(LetterRequest $letterRequest)
    {
        $letterRequest->loadMissing(['user', 'type']);

        $path = $letterRequest->pdf_path;

        if (empty($path) || !Storage::disk('public')->exists($path)) {
            try {
                $path = $this->generatePdf($letterRequest);
                $letterRequest->update(['pdf_path' => $path]);
            } catch (\Throwable $e) {
                return redirect()
                    ->route('letter-requests.show', $letterRequest)
                    ->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
            }
        }

        if (empty($path)) {
            return redirect()
                ->route('letter-requests.show', $letterRequest)
                ->with('error', 'Path file PDF masih kosong.');
        }

        if (!Storage::disk('public')->exists($path)) {
            return redirect()
                ->route('letter-requests.show', $letterRequest)
                ->with('error', 'File PDF tidak ditemukan di storage.');
        }

        return Storage::disk('public')->download($path);
    }

    /**
     * Generate PDF dari template HTML dan simpan ke storage.
     */
    protected function generatePdf(LetterRequest $letterRequest): string
    {
        $letterRequest->loadMissing(['user', 'type']);

        // ==========================
        // Auto-lengkapi field umum
        // ==========================
        $data = is_array($letterRequest->data_input) ? $letterRequest->data_input : [];

        $currentTenant = app(\App\Support\Tenancy\TenantManager::class)->current();
        $tenantName = $currentTenant?->name ?? 'Perumahan HOMI';
        $initials = 'HM';
        if ($currentTenant) {
            preg_match_all('/\b\w/', $currentTenant->name, $matches);
            $initials = strtoupper(implode('', $matches[0]));
        }

        // Nomor Surat: kalau belum ada, auto generate
        if (empty($data['nomor_surat'])) {
            $seq = str_pad((string) $letterRequest->id, 3, '0', STR_PAD_LEFT);
            $rt  = $data['rt'] ?? '01';
            $rw  = $data['rw'] ?? '01';
            $data['nomor_surat'] = $seq . "/RT{$rt}-RW{$rw}/{$initials}/" . date('Y');
        }

        if (empty($data['tanggal_surat'])) {
            $data['tanggal_surat'] = now()->translatedFormat('d F Y');
        }

        // Default nama
        if (empty($data['nama'])) {
            $data['nama'] = $letterRequest->user?->full_name ?? $letterRequest->user?->name ?? 'Warga';
        }
        $data['nama_warga'] = $data['nama_warga'] ?? $data['nama'];

        if (empty($data['nik'])) {
            $data['nik'] = $letterRequest->user?->residentProfile?->nik ?? '';
        }

        // Default alamat
        if (empty($data['alamat'])) {
            $rp = $letterRequest->user?->residentProfile ?? null;
            $data['alamat'] = $rp?->alamat ?: $tenantName;
        }

        // Default RT/RW
        $data['rt'] = $data['rt'] ?? '01';
        $data['rw'] = $data['rw'] ?? '01';

        // Default nama_rt
        $data['nama_rt'] = $data['nama_rt'] ?? 'Ketua RT';
        $data['nama_perumahan'] = $data['nama_perumahan'] ?? $tenantName;

        // Simpan balik
        $letterRequest->data_input = $data;
        $letterRequest->save();

        $html = $letterRequest->renderHtml();

        $htmlWrapper = view('letter_requests.pdf_base', [
            'html'       => $html,
            'item'       => $letterRequest,
            'tenantName' => $tenantName,
        ])->render();

        $pdf = Pdf::loadHTML($htmlWrapper)->setPaper('A4', 'portrait');

        $userName = str_replace(' ', '_', $letterRequest->user?->full_name ?? $letterRequest->user?->username ?? 'warga');

        $typeSlug = Str::slug($letterRequest->type->name ?? 'surat');

        $fileName = $typeSlug . '-' . $userName . '-' . now()->format('YmdHis') . '.pdf';
        $path     = 'surat/' . date('Y/m') . '/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * API MOBILE: pilih type_id + data_input → simpan → generate PDF
     * POST /api/letter-requests/generate
     */
    public function apiCreateAndGenerate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type_id'     => ['required', 'integer', 'exists:letter_types,id'],
            'perihal'     => ['nullable', 'string', 'max:255'],
            'data_input'  => ['nullable', 'array'],
        ]);

        $user = $request->user();
        $type = LetterType::findOrFail($data['type_id']);

        $resident = $user->residentProfile ?? null;

        $currentTenant = app(\App\Support\Tenancy\TenantManager::class)->current();
        $tenantName = $currentTenant?->name ?? 'Perumahan HOMI';
        $initials = 'HM';
        if ($currentTenant) {
            preg_match_all('/\b\w/', $currentTenant->name, $matches);
            $initials = strtoupper(implode('', $matches[0]));
        }

        // default umum
        $auto = [
            'nomor_surat'    => '___/' . $initials . '/___/' . date('Y'),
            'tanggal_surat'  => now()->translatedFormat('d F Y'),
            'nama'           => $user->full_name ?? $user->username ?? '',
            'nama_warga'     => $user->full_name ?? $user->username ?? '',
            'nik'            => $resident->nik ?? '',
            'alamat'         => $resident->alamat
                ?? (($resident && ($resident->blok || $resident->no_rumah))
                    ? trim(($resident->blok ?? '') . ' ' . ($resident->no_rumah ?? ''))
                    : $tenantName),
            'no_telepon'     => $user->phone ?? '',
            'keperluan'      => $data['perihal'] ?? 'Pengajuan layanan melalui aplikasi HOMI',
            'tujuan'         => 'Kelurahan/Instansi terkait',
            'pejabat'        => 'Admin Perumahan',
            'jabatan'        => 'Pengelola ' . $tenantName,
            'nama_perumahan' => $tenantName,
        ];

        // data dari mobile
        $incoming = $request->input('data_input', []);
        if (!is_array($incoming)) $incoming = [];

        // ✅ INI KUNCI: simpan SEMUA key (biar semua surat aman)
        $dataInput = array_merge($auto, $incoming);

        // ✅ Validasi required_json (format DB kamu: ["field1","field2",...])
        $requiredFields = method_exists($type, 'requiredFields')
            ? $type->requiredFields()
            : (is_array($type->required_json) ? $type->required_json : []);

        $missing = [];
        foreach ($requiredFields as $field) {
            if (!is_string($field) || $field === '') continue;
            $val = $dataInput[$field] ?? null;
            if ($val === null || $val === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            return response()->json([
                'message' => 'Data surat belum lengkap (sesuai required_json).',
                'missing' => $missing,
            ], 422);
        }

        $lr = LetterRequest::create([
            'user_id'    => $user->id,
            'type_id'    => $type->id,
            'status'     => 'submitted',
            'data_input' => $dataInput,
            'pdf_path'   => null,
        ]);

        $path = $this->generatePdf($lr);
        $lr->update(['pdf_path' => $path]);

        return response()->json([
            'message' => 'Pengajuan dibuat & PDF berhasil digenerate.',
            'id'      => $lr->id,
            'status'  => $lr->status,
            'pdf_url' => asset('storage/' . $path),
        ]);
    }
}
