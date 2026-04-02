<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeePaymentController extends Controller
{
/*************  ✨ Windsurf Command 🌟  *************/
    public function pay(Request $request, $invoiceId)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'note'        => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        $invoice = FeeInvoice::query()->findOrFail($invoiceId);

        if ((int)$invoice->user_id !== (int)$user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // kalau sudah paid/approved, stop
        if (in_array(strtolower($invoice->status), ['paid', 'approved'], true)) {
            return response()->json(['message' => 'Invoice already paid'], 422);
        }

        // kalau sudah pending, opsional: block biar tidak spam upload
        if (strtolower($invoice->status) === 'pending') {
            return response()->json(['message' => 'Invoice already pending review'], 422);
        }

        $result = DB::transaction(function () use ($request, $invoice, $user) {
            $path = $request->file('proof_image')->store('payment_proofs', 'public');

            $payment = FeePayment::query()->create([
                'invoice_id'    => $invoice->id,
                'payer_user_id' => $user->id,
                'proof_path'    => $path,
                'note'          => $request->input('note'),
                'review_status' => 'pending',
            ]);

            // --- AUTO VALIDATION OCR ---
            $ocrService = app(\App\Services\OcrService::class);
            $ocrResult = $ocrService->validatePayment($payment);

            if ($ocrResult && $ocrResult['match']) {
                $payment->update([
                    'review_status' => 'approved',
                    'reviewed_by'   => 0, // System
                    'reviewed_at'   => now(),
                    'note'          => '[AUTO APPROVED] Menemukan nominal sesuai: Rp ' . number_format($ocrResult['amount'], 0, ',', '.')
                ]);

                $invoice->update(['status' => 'paid']);
                $isAutoApproved = true;
            } else {
                $invoice->update(['status' => 'pending']);
                $isAutoApproved = false;
            }

            return [
                'payment' => $payment,
                'auto_approved' => $isAutoApproved,
                'ocr_text' => $ocrResult['text'] ?? null
            ];
        });

        // --- SEND FCM NOTIFICATION ---
        if ($user->fcm_token) {
            $fcm = new FirebaseService();
            if ($result['auto_approved']) {
                $periodStr = $invoice->period instanceof \Carbon\Carbon 
                    ? $invoice->period->format('F Y') 
                    : (is_string($invoice->period) ? $invoice->period : 'N/A');
                
                $fcm->sendNotification(
                    $user->fcm_token,
                    "Pembayaran Iuran Berhasil",
                    "Pembayaran untuk periode {$periodStr} telah dikonfirmasi otomatis. Terima kasih!"
                );
            } else {
                $fcm->sendNotification(
                    $user->fcm_token,
                    "Bukti Pembayaran Terkirim",
                    "Bukti pembayaran Anda sedang dalam antrean verifikasi admin. Mohon tunggu."
                );
            }
        }

        return response()->json([
            'message' => $result['auto_approved'] 
                ? 'Pembayaran Anda berhasil divalidasi dan disetujui otomatis oleh sistem.' 
                : 'Bukti terkirim. Menunggu verifikasi admin (OCR tidak dapat memvalidasi otomatis).',
            'data' => [
                'payment_id' => $result['payment']->id,
                'status'     => $result['payment']->review_status,
                'proof_url'  => asset('storage/' . $result['payment']->proof_path),
            ],
        ], 201);
    }
/*******  ed6519d9-06b5-4129-81cc-cb9f1c8cc697  *******/

    public function history(Request $request)
    {
        $user = $request->user();

        $items = FeeInvoice::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['paid', 'approved'])
            ->with('feeType:id,name')
            ->latest()
            ->get()
            ->map(fn ($inv) => [
                'id'        => $inv->id,
                'fee_type'  => $inv->feeType?->name,
                'period'    => is_string($inv->period) ? substr($inv->period, 0, 7) : ($inv->period?->format('Y-m')),
                'amount'    => $inv->amount,
                'status'    => $inv->status,
                'trx_id'    => $inv->trx_id,
            ]);

        return response()->json(['data' => $items]);
    }
}
