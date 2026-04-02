<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class FeePaymentReviewController extends Controller
{
    public function pending()
    {
        $data = FeePayment::query()
            ->where('review_status', 'pending')
            ->with([
                'invoice:id,user_id,fee_type_id,period,amount,status,trx_id',
                'invoice.feeType:id,name',
                'payer:id,name,email'
            ])
            ->latest()
            ->paginate(20);

        $data->getCollection()->transform(function ($p) {
            $p->proof_url = asset('storage/' . $p->proof_path);
            return $p;
        });

        return response()->json($data);
    }

    public function approve(Request $request, $paymentId)
    {
        $payment = FeePayment::query()->with('invoice')->findOrFail($paymentId);

        if ($payment->review_status !== 'pending') {
            return response()->json(['message' => 'Payment already reviewed'], 422);
        }

        $payment->update([
            'review_status' => 'approved',
            'reviewed_by'   => $request->user()->id,
            'reviewed_at'   => now(),
        ]);

        if ($payment->invoice) {
            $payment->invoice->update(['status' => 'paid']);
        }

        // Send FCM Notification
        if ($payment->payer && $payment->payer->fcm_token) {
            $fcm = new FirebaseService();
            $fcm->sendNotification(
                $payment->payer->fcm_token,
                "Pembayaran Disetujui",
                "Pembayaran iuran Anda untuk periode {$payment->invoice->period} telah disetujui. Terima kasih!"
            );
        }

        return response()->json(['message' => 'Payment approved']);
    }

    public function reject(Request $request, $paymentId)
    {
        $request->validate(['reason' => 'nullable|string|max:255']);

        $payment = FeePayment::query()->with('invoice')->findOrFail($paymentId);

        if ($payment->review_status !== 'pending') {
            return response()->json(['message' => 'Payment already reviewed'], 422);
        }

        $payment->update([
            'review_status' => 'rejected',
            'note'          => $request->input('reason') ?? $payment->note,
            'reviewed_by'   => $request->user()->id,
            'reviewed_at'   => now(),
        ]);

        if ($payment->invoice) {
            $payment->invoice->update(['status' => 'rejected']);
        }

        // Send FCM Notification
        if ($payment->payer && $payment->payer->fcm_token) {
            $fcm = new FirebaseService();
            $reason = $request->input('reason') ?? "Data tidak sesuai";
            $fcm->sendNotification(
                $payment->payer->fcm_token,
                "Pembayaran Ditolak",
                "Pembayaran iuran Anda ditolak. Alasan: {$reason}. Silakan hubungi admin bila perlu."
            );
        }

        return response()->json(['message' => 'Payment rejected']);
    }
}
