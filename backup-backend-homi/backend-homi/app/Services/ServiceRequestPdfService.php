<?php

namespace App\Services;

use App\Models\ServiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceRequestPdfService
{
    public function generate(ServiceRequest $sr): string
    {
        $sr->loadMissing(['type.letterType', 'user.residentProfile']);

        $letterType = $sr->type?->letterType;
        if (!$letterType) {
            throw new \RuntimeException('Jenis pengajuan belum terhubung ke template surat.');
        }

        $templateHtml = (string) ($letterType->template_html ?? '');
        if (trim($templateHtml) === '') {
            throw new \RuntimeException('Template HTML surat kosong.');
        }

        // data_input bisa null/string/array
        $data = $sr->data_input;
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $data = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }
        $data = is_array($data) ? $data : [];

        // auto default jika field belum diisi
        $data['nama'] = $data['nama'] ?? ($sr->user->full_name ?? $sr->user->name ?? '');
        $data['alamat'] = $data['alamat'] ?? ($sr->user->residentProfile->alamat ?? 'Perumahan Hawai Garden Batam Center');
        $data['rt'] = $data['rt'] ?? '01';
        $data['rw'] = $data['rw'] ?? '01';
        $data['nama_rt'] = $data['nama_rt'] ?? 'Ketua RT';
        $data['tanggal_surat'] = $data['tanggal_surat'] ?? now()->translatedFormat('d F Y');

        // nomor surat (contoh yang kamu pakai)
        if (empty($data['nomor_surat'])) {
            $seq = str_pad((string) $sr->id, 3, '0', STR_PAD_LEFT);
            $data['nomor_surat'] = "{$seq}/RT{$data['rt']}-RW{$data['rw']}/HG/" . date('Y');
        }

        // replace placeholder {{field}}
        $filled = $this->replacePlaceholders($templateHtml, $data);

        // ambil <style> (kalau template punya)
        $extraCss = $this->extractCss($filled);

        // ambil <body> doang (kalau template full HTML)
        $bodyOnly = $this->extractBody($filled);

        // render wrapper kop + logo kiri
        $finalHtml = view('pdf.kop_surat', [
            'body' => $bodyOnly,
            'extraCss' => $extraCss,

            // bebas kamu ganti tulisannya biar mirip contoh
            'kopLine1' => 'PENGELOLA PERUMAHAN HAWAI GARDEN',
            'kopLine2' => 'KETUA RT / RW',
            'kopLine3' => 'KOTA BATAM',
            'kopLine4' => 'Hawai Garden Batam Center',
        ])->render();

        $pdf = Pdf::loadHTML($finalHtml)->setPaper('A4', 'portrait');

        $typeSlug = Str::slug($letterType->name ?? 'surat');
        $userName = Str::slug($sr->user->full_name ?? $sr->user->name ?? 'warga');
        $fileName = "{$typeSlug}-{$userName}-" . now()->format('YmdHis') . ".pdf";
        $path = 'surat/' . date('Y/m') . '/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    private function replacePlaceholders(string $html, array $data): string
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) $v = json_encode($v);
            $html = str_replace('{{' . $k . '}}', e($v), $html);
        }
        return $html;
    }

    private function extractCss(string $html): string
    {
        if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $m)) {
            return implode("\n", $m[1]);
        }
        return '';
    }

    private function extractBody(string $html): string
    {
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $m)) {
            return $m[1];
        }
        return $html;
    }
}
