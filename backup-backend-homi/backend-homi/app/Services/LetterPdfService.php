<?php

namespace App\Services;

use App\Models\LetterType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LetterPdfService
{
    /**
     * Generate PDF dari LetterType + data, simpan ke storage public, return path relatif.
     */
    public function generate(LetterType $type, array $data, string $userName = 'warga'): string
    {
        $html = $this->renderTemplate($type->template_html ?? '', $data);

        // wrapper sederhana (biar dompdf rapi)
        $wrapped = <<<HTML
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.4; }
    .page { padding: 18px; }
  </style>
</head>
<body>
  <div class="page">
    {$html}
  </div>
</body>
</html>
HTML;

        $pdf = Pdf::loadHTML($wrapped)->setPaper('A4', 'portrait');

        $safeUser = Str::slug(str_replace(' ', '_', $userName)) ?: 'warga';
        $typeSlug = Str::slug($type->name ?? 'surat') ?: 'surat';

        $fileName = $typeSlug . '-' . $safeUser . '-' . now()->format('YmdHis') . '.pdf';
        $path     = 'surat/' . date('Y/m') . '/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Replace {{key}} di template_html dengan value yang sudah di-escape.
     */
    public function renderTemplate(string $templateHtml, array $data): string
    {
        $tpl = (string) $templateHtml;
        if ($tpl === '') return '';

        foreach ($data as $k => $v) {
            $key = (string) $k;
            $val = is_scalar($v) ? (string) $v : json_encode($v);

            $safe = e($val);
            $tpl = str_replace('{{'.$key.'}}', $safe, $tpl);
            $tpl = str_replace('{{ '.$key.' }}', $safe, $tpl);
        }

        return $tpl;
    }
}
