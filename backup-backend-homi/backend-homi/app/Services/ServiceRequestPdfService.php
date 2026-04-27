<?php

namespace App\Services;

use App\Models\ServiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceRequestPdfService
{
    public function renderHtml(ServiceRequest $sr): string
    {
        $sr->loadMissing(['type.letterType', 'user.residentProfile']);

        // ✅ Ambil data lengkap (termasuk auto-fill dari profil)
        $fullData = $this->buildData($sr);
        
        // ✅ Masukkan ke model agar renderHtml() di model bisa baca
        $sr->data_input = $fullData;

        $letterType = $sr->type?->letterType;
        $templateHtml = (string) ($letterType->template_html ?? '');

        $currentTenant = app(\App\Support\Tenancy\TenantManager::class)->current();
        $tenantName = $currentTenant?->name ?? 'Perumahan HOMI';

        if ($letterType && trim($templateHtml) !== '') {
            // ✅ KUNCI: Gunakan logika renderHtml di model (robust regex)
            $filled = $sr->renderHtml();
            $extraCss = $this->extractCss($filled);
            $bodyOnly = $this->extractBody($filled);
        } else {
            $extraCss = '';
            $bodyOnly = view('pdf.service_request', [
                'sr' => $sr,
                'data' => $fullData,
                'typeName' => $sr->type?->name ?? 'SURAT KETERANGAN',
                'reporter' => $sr->reporter_name ?? $fullData['nama'] ?? '-',
                'subject' => $sr->subject ?? '-',
                'place' => $sr->place ?? '-',
                'data_input_grid' => $sr->data_input
            ])->render();
        }

        return view('pdf.kop_surat', [
            'body' => $bodyOnly,
            'extraCss' => $extraCss,
            'kopLine1' => 'PENGELOLA ' . strtoupper($tenantName),
            'kopLine2' => 'KETUA RT',
            'kopLine3' => 'KOTA BATAM',
            'kopLine4' => $tenantName . ' Batam',
        ])->render();
    }

    public function generate(ServiceRequest $sr): string
    {
        $finalHtml = $this->renderHtml($sr);
        $pdf = Pdf::loadHTML($finalHtml)->setPaper('A4', 'portrait');

        $letterType = $sr->type?->letterType;
        $typeSlug = Str::slug($letterType->name ?? $sr->type?->name ?? 'surat');
        $userName = Str::slug($sr->user->full_name ?? $sr->user->name ?? 'warga');
        $fileName = "{$typeSlug}-{$userName}-" . now()->format('YmdHis') . ".pdf";
        $path = 'surat/' . date('Y/m') . '/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function resolveExistingPublicPath(?string $rawPath): ?string
    {
        if (!is_string($rawPath) || trim($rawPath) === '') {
            return null;
        }

        $path = trim(str_replace('\\', '/', $rawPath));
        $candidates = [];

        $candidates[] = ltrim($path, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $urlPath = parse_url($path, PHP_URL_PATH);
            if (is_string($urlPath) && trim($urlPath) !== '') {
                $candidates[] = ltrim($urlPath, '/');
            }
        }

        foreach ($candidates as $candidate) {
            $candidate = preg_replace('#^public/#', '', $candidate);
            $candidate = preg_replace('#^storage/#', '', $candidate);

            if (!$candidate) {
                continue;
            }

            if (Storage::disk('public')->exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    public function buildData(ServiceRequest $sr): array
    {
        // data_input bisa null/string/array
        $raw = $sr->data_input;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }
        $raw = is_array($raw) ? $raw : [];

        // ✅ Kumpulkan semua data awal (alias snake vs camel)
        $data = [];
        foreach ($raw as $k => $v) {
            $key = trim((string) $k);
            $val = is_scalar($v) ? (string) $v : json_encode($v, JSON_UNESCAPED_UNICODE);
            foreach (array_unique([$key, Str::snake($key), Str::camel($key)]) as $alias) {
                if ($alias !== '') $data[$alias] = $val;
            }
        }

        // ✅ Fallback Nama & NIK (Prioritas: Input > ResidentProfile > Generic)
        $rawName = trim((string)($data['nama'] ?? ($data['nama_warga'] ?? ($data['nama_pemohon'] ?? ($sr->reporter_name ?? '')))));
        $isGeneric = empty($rawName) || str_contains(strtolower($rawName), 'warga');
        
        $userFullName = trim((string)($sr->user->full_name ?? ($sr->user->name ?? '')));
        $isUserGeneric = empty($userFullName) || str_contains(strtolower($userFullName), 'warga');

        if (!$isGeneric) {
            $data['nama'] = $rawName;
        } elseif (!$isUserGeneric) {
            $data['nama'] = $userFullName;
        } else {
            $data['nama'] = '....................';
        }

        $data['nik'] = $data['nik'] ?? ($sr->user->residentProfile->nik ?? '....................');
        
        // ✅ Alamat & Perumahan
        $currentTenant = app(\App\Support\Tenancy\TenantManager::class)->current();
        $perumahan = $currentTenant?->name ?? "Perumahan HOMI";
        $data['nama_perumahan'] = $perumahan;

        $blok = $data['blok'] ?? ($sr->user->residentProfile->blok ?? '...');
        $noRumah = $data['no_rumah'] ?? ($data['no_rumah'] ?? ($sr->user->residentProfile->no_rumah ?? ''));
        $cleanNoRumah = trim((string)$noRumah);
        $suffixNo = ($cleanNoRumah === '-' || $cleanNoRumah === '') ? '' : " No. {$cleanNoRumah}";

        if (Str::contains(Str::lower($blok), Str::lower($perumahan))) {
            $data['alamat'] = "{$blok}{$suffixNo}";
        } else {
            $data['alamat'] = "{$perumahan} Blok {$blok}{$suffixNo}";
        }
        
        // ✅ Tempat/Tanggal Lahir (Formatted)
        $tempatLahir = $data['tempat_lahir'] ?? ($sr->user->residentProfile->tempat_lahir ?? '..........');
        $tanggalLahirRaw = $data['tanggal_lahir'] ?? ($sr->user->residentProfile->tanggal_lahir ?? null);
        $data['tmpt_tgl_lahir'] = $this->formatBirthInfo($tempatLahir, $tanggalLahirRaw);
        
        $data['tempat_lahir'] = $tempatLahir;
        $data['tanggal_lahir'] = $this->formatDateIndo($tanggalLahirRaw);

        // ✅ Kompatibilitas Khusus "Surat Keterangan Usaha"
        // Mobile kirim 'jenis_usaha', template expect 'bidang_usaha'
        $data['bidang_usaha'] = $data['bidang_usaha'] ?? ($data['jenis_usaha'] ?? '....................');
        $data['nama_usaha'] = $data['nama_usaha'] ?? '....................';
        $data['alamat_usaha'] = $data['alamat_usaha'] ?? '....................';
        $data['lama_usaha'] = $data['lama_usaha'] ?? '....................';

        // ✅ Kompatibilitas Khusus "Surat Kematian"
        $data['nama_alm'] = $data['nama_alm'] ?? '....................';
        $data['nik_alm'] = $data['nik_alm'] ?? '....................';
        $data['alamat_alm'] = $data['alamat_alm'] ?? '....................';
        $data['penyebab'] = $data['penyebab'] ?? 'Sakit / Lainnya';
        $data['hubungan'] = $data['hubungan'] ?? 'Keluarga';
        $data['tempat_kematian'] = $data['tempat_kematian'] ?? '....................';
        
        $tglMate = $data['tanggal_kematian'] ?? null;
        $data['tanggal_kematian'] = $this->formatDateIndo($tglMate);

        $tempatLahirAlm = $data['tempat_lahir_alm'] ?? '..........';
        $tglLahirAlmRaw = $data['tanggal_lahir_alm'] ?? null;
        $data['tmpt_tgl_lahir_alm'] = $this->formatBirthInfo($tempatLahirAlm, $tglLahirAlmRaw);

        $data['nama_pelapor'] = $data['nama_pelapor'] ?? $data['nama'];
        $data['nik_pelapor'] = $data['nik_pelapor'] ?? $data['nik'];

        // ✅ Info RT/RW
        $data['rt'] = $data['rt'] ?? ($sr->user->residentProfile->rt ?? '00... ');
        $data['rw'] = $data['rw'] ?? ($sr->user->residentProfile->rw ?? '00... ');
        $data['nama_rt'] = $data['nama_rt'] ?? ($sr->user->residentProfile->nama_rt ?? '....................');
        $data['pj_label'] = "KETUA RT";
        
        // ✅ Status Lainnya
        $data['agama'] = $data['agama'] ?? ($data['religion'] ?? 'Islam');
        $data['status_perkawinan'] = $data['status_perkawinan'] ?? ($data['marital_status'] ?? 'Belum Kawin');
        $data['pekerjaan'] = $data['pekerjaan'] ?? ($sr->user->residentProfile->pekerjaan ?? '....................');
        $data['kewarganegaraan'] = $data['kewarganegaraan'] ?? ($data['nationality'] ?? 'Indonesia');
        $data['jenis_kelamin'] = $data['jenis_kelamin'] ?? ($sr->user->residentProfile->jenis_kelamin ?? 'Laki-laki / Perempuan');
        
        // ✅ Meta Surat
        $data['tanggal_surat'] = now()->translatedFormat('d F Y');
        $data['keperluan'] = $data['keperluan'] ?? ($sr->subject ?? '....................');
        $data['tujuan_instansi'] = $data['tujuan_instansi'] ?? ($data['tujuan'] ?? 'Kelurahan / Instansi Terkait');

        if (empty($data['nomor_surat'])) {
            $isLayanan = !($sr->type?->letter_type_id);
            $prefix = $isLayanan ? 'LYN' : 'SKU';
            $seq = str_pad((string) $sr->id, 3, '0', STR_PAD_LEFT);
            $data['nomor_surat'] = "{$seq}/{$prefix}/" . date('m') . "-MS/" . $this->romanMonth(date('n')) . "/" . date('Y');
        }

        return $data;
    }

    private function formatBirthInfo(string $place, $date): string
    {
        $formattedDate = $this->formatDateIndo($date);
        return "{$place}, {$formattedDate}";
    }

    private function formatDateIndo($date): string
    {
        if (!$date) return '..-..-....';
        try {
            if (is_string($date) && str_contains($date, 'T')) {
                $date = explode('T', $date)[0];
            }
            return \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
        } catch (\Exception $e) {
            return (string)$date;
        }
    }

    private function romanMonth(int $month): string
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        return $map[$month] ?? (string)$month;
    }



    public function replacePlaceholders(string $html, array $data): string
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) $v = json_encode($v);
            $html = str_replace('{{' . $k . '}}', e($v), $html);
        }
        return $html;
    }

    public function extractCss(string $html): string
    {
        if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $m)) {
            return implode("\n", $m[1]);
        }
        return '';
    }

    public function extractBody(string $html): string
    {
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $m)) {
            return $m[1];
        }
        return $html;
    }
}
