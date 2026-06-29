<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment as Payment;
use App\Services\OcrService;
use Illuminate\Http\Request;

class PaymentOcrController extends Controller
{
    protected $ocrService;

    public function __construct(OcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    /**
     * Trigger OCR Scan untuk bukti pembayaran tertentu
     */
    public function scan(Payment $payment)
    {
        if (!$payment->proof_path) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti pembayaran tidak ditemukan.'
            ], 400);
        }

        $result = $this->ocrService->scanFile($payment->proof_path);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 500);
        }

        $rawText = $result['text'];
        $amountFound = $this->ocrService->extractAmount($rawText);
        
        // Ambil nominal tagihan
        $invoiceAmount = $payment->invoice?->amount ?? 0;
        
        // Cek kecocokan
        $isMatch = ($amountFound && $amountFound == $invoiceAmount);

        return response()->json([
            'success' => true,
            'text' => $rawText,
            'amount_found' => $amountFound,
            'amount_formatted' => $amountFound ? 'Rp ' . number_format($amountFound, 0, ',', '.') : 'Tidak ditemukan',
            'is_match' => $isMatch,
            'invoice_amount' => $invoiceAmount
        ]);
    }
}
