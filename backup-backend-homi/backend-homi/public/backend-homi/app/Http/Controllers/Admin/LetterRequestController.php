<?php

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
        $data = $letterRequest->data_input ?? [];

        // Nomor Surat: kalau belum ada, auto generate
        if (empty($data['nomor_surat'])) {
            // contoh: 001/RT01-RW02/HG/2025
            $seq = str_pad((string) $letterRequest->id, 3, '0', STR_PAD_LEFT);
            $rt  = $data['rt'] ?? '01';
            $rw  = $data['rw'] ?? '01';
            $data['nomor_surat'] = $seq . "/RT{$rt}-RW{$rw}/HG/" . date('Y');
        }

        if (empty($data['tanggal_surat'])) {
            $data['tanggal_surat'] = now()->translatedFormat('d F Y');
        }

        // Default nama (kalau mobile belum ngirim)
        if (empty($data['nama'])) {
            $data['nama'] = $letterRequest->user->full_name ?? $letterRequest->user->name ?? '';
        }

        // Default alamat (kalau mobile belum ngirim)
        if (empty($data['alamat'])) {
            $rp = $letterRequest->user->residentProfile ?? null;
            $data['alamat'] = $rp?->alamat ?: 'Perumahan Hawai Garden';
        }

        // Default RT/RW (kalau belum ada)
        $data['rt'] = $data['rt'] ?? '01';
        $data['rw'] = $data['rw'] ?? '01';

        // Default pejabat penandatangan
        $data['nama_rt'] = $data['nama_rt'] ?? 'Ketua RT';

        // Simpan balik biar halaman admin juga ikut rapi
        $letterRequest->data_input = $data;
        $letterRequest->save();

        $html = $letterRequest->renderHtml();

        $htmlWrapper = view('letter_requests.pdf_base', [
            'html' => $html,
            'item' => $letterRequest,
        ])->render();

        $pdf = Pdf::loadHTML($htmlWrapper)->setPaper('A4', 'portrait');

        $userName = str_replace(' ', '_', $letterRequest->user->full_name ?? $letterRequest->user->username ?? 'warga');

        // ✅ FIX: kamu gak punya kolom slug di letter_types
        $typeSlug = Str::slug($letterRequest->type->name ?? 'surat');

        $fileName = $typeSlug . '-' . $userName . '-' . now()->format('YmdHis') . '.pdf';
        $path     = 'surat/' . date('Y/m') . '/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * ✅ API MOBILE: pilih type_id → auto isi data_input → langsung generate PDF
     * POST /api/letter-requests/generate
     */
    public function apiCreateAndGenerate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type_id' => ['required', 'integer', 'exists:letter_types,id'],
            'perihal' => ['nullable', 'string', 'max:255'],
            'data_input' => ['nullable', 'array'],
        ]);

        $user = $request->user();
        $type = LetterType::findOrFail($data['type_id']);

        // relasi residentProfile (sesuai migrations di project kamu)
        $resident = $user->residentProfile ?? null;

        // data auto (tanpa form)
        $auto = [
            'nomor_surat'   => '___/HG/___/' . date('Y'),
            'tanggal_surat' => now()->translatedFormat('d F Y'),
            'nama'          => $user->full_name ?? $user->username ?? '',
            'nik'           => '', // NIK tidak ada di residentProfile bawaan
            'alamat'        => $resident->alamat ?? (($resident && ($resident->blok || $resident->no_rumah))
                                ? trim(($resident->blok ?? '') . ' ' . ($resident->no_rumah ?? ''))
                                : 'Perumahan Hawai Garden'),
            'no_telepon'    => $user->phone ?? '',
            'keperluan'     => $data['perihal'] ?? 'Pengajuan layanan melalui aplikasi HOMI',
            'tujuan'        => 'Kelurahan/Instansi terkait',
            'pejabat'       => 'Admin Perumahan',
            'jabatan'       => 'Pengelola Perumahan Hawai Garden',
        ];

        // Optional: kalau mobile ngirim data_input (mis: rt, rw, nama_rt, dll), pakai itu untuk override
        $incoming = $request->input('data_input');
        if (is_array($incoming)) {
            foreach ($incoming as $k => $v) {
                $auto[$k] = $v;
            }
        }

        // isi sesuai required_json biar sesuai template masing2 surat
        $fields = ($type->required_json['fields'] ?? []);
        $dataInput = [];

        foreach ($fields as $f) {
            $name = $f['name'] ?? null;
            if (!$name) continue;

            $dataInput[$name] = $auto[$name] ?? '';
        }

        // buat record request
        $lr = LetterRequest::create([
            'user_id'    => $user->id,
            'type_id'    => $type->id,
            'status'     => 'submitted',
            'data_input' => $dataInput,
            'pdf_path'   => null,
        ]);

        // generate pdf & simpan path
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
