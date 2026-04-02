<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OcrService
{
    protected $apiKey;
    protected $apiUrl = 'https://api.ocr.space/parse/image';

    public function __construct()
    {
        // Gunakan key dari .env atau fallback ke key publik (terbatas) jika belum diset
        $this->apiKey = env('OCR_SPACE_API_KEY', 'helloworld'); 
    }

    /**
     * Scan gambar dan kembalikan teks hasil ekstraksi
     */
    public function scanFile($filePath)
    {
        if (!Storage::disk('public')->exists($filePath)) {
            return [
                'success' => false,
                'error' => 'File tidak ditemukan di storage (public).'
            ];
        }

        try {
            $fileContent = Storage::disk('public')->get($filePath);
            $fileName = basename($filePath);

            $response = Http::asMultipart()
                ->post($this->apiUrl, [
                    'apikey' => $this->apiKey,
                    'language' => 'ind', // Bahasa Indonesia
                    'isOverlayRequired' => 'false',
                    'file' => [
                        'name' => $fileName,
                        'contents' => $fileContent
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['ParsedResults'][0]['ParsedText'])) {
                    return [
                        'success' => true,
                        'text' => $data['ParsedResults'][0]['ParsedText']
                    ];
                }

                return [
                    'success' => false,
                    'error' => $data['ErrorMessage'][0] ?? 'Gagal memproses gambar.'
                ];
            }

            return [
                'success' => false,
                'error' => 'Gagal menghubungi server OCR (Status: ' . $response->status() . ').'
            ];

        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan sistem saat scan OCR.'
            ];
        }
    }

    /**
     * Ekstrak nominal dari teks mentah menggunakan Regex yang lebih kuat
     */
    public function extractAmount($text)
    {
        // Bersihkan karakter aneh hasil OCR yang sering muncul (misal: | menjadi 1, dsb)
        $cleaned = str_replace(["\n", "\r", "|", "!", "[", "]"], " ", $text);
        
        // Pola regex lebih lengkap:
        // 1. Mencari angka dengan format ribuan (titik atau koma di Indonesia sering tertukar di OCR)
        // 2. Mencari angka yang berurutan minimal 5 digit (biasanya iuran >= 10.000)
        // 3. Menghindari angka tanggal/waktu (misal: 2025, 12:00)
        
        // Cari pola Rp 150.000 atau 150.000 atau 150,000
        preg_match_all('/(?:Rp|RP|IDR)?\s*(\d{1,3}(?:[.,]\d{3})+)/i', $cleaned, $matches);
        
        $foundAmounts = [];
        if (!empty($matches[1])) {
            foreach ($matches[1] as $val) {
                // Konversi ke integer (hapus pemisah ribuan)
                $num = (int) str_replace(['.', ','], '', $val);
                if ($num >= 1000) { $foundAmounts[] = $num; }
            }
        }

        // Fallback: Cari deretan angka murni tanpa pemisah yang panjangnya 5-8 digit
        preg_match_all('/\b\d{5,8}\b/', $cleaned, $pureMatches);
        if (!empty($pureMatches[0])) {
            foreach ($pureMatches[0] as $val) {
                $foundAmounts[] = (int) $val;
            }
        }
        
        return !empty($foundAmounts) ? max($foundAmounts) : null;
    }

    /**
     * Logika Validasi Otomatis untuk Controller
     */
    public function validatePayment($payment)
    {
        if (!$payment->proof_path) return false;

        $result = $this->scanFile($payment->proof_path);
        if (!$result['success']) return false;

        $text = $result['text'];
        $amountFound = $this->extractAmount($text);
        $invoiceAmount = $payment->invoice?->amount ?? 0;

        if ($amountFound && $amountFound == $invoiceAmount) {
            return [
                'match' => true,
                'amount' => $amountFound,
                'text' => $text
            ];
        }

        return [
            'match' => false,
            'amount' => $amountFound,
            'text' => $text
        ];
    }
}
