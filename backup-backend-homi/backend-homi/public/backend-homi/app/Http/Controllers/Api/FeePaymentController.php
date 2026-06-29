<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
    /**
     * WARGA: upload bukti bayar untuk invoice miliknya
     * form-data:
     * - proof_image (file)
     * - note (optional)
     */
    public function pay(Request $request, $invoiceId)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'note'        => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        // ambil invoice
        $invoice = FeeInvoice::findOrFail($invoiceId);

        // pastikan invoice milik user ini
        if ((int)$invoice->user_id !== (int)$user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // kalau sudah paid, tidak boleh upload lagi
        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Invoice already paid'], 422);
        }

        // simpan file
        $path = $request->file('proof_image')->store('payment_proofs', 'public');

        $payment = FeePayment::create([
            'invoice_id'     => $invoice->id,
            'payer_user_id'  => $user->id,
            'proof_path'     => $path,
            'note'           => $request->input('note'),
            'review_status'  => 'pending',
        ]);

        // update status invoice jadi pending (menunggu admin)
        $invoice->update(['status' => 'pending']);

        return response()->json([
            'message' => 'Proof uploaded, waiting for admin review',
            'data' => [
                'payment_id' => $payment->id,
                'proof_url'  => asset('storage/' . $payment->proof_path),
            ],
        ], 201);
    }

    /**
     * WARGA: riwayat pembayaran (paid)
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $items = FeeInvoice::query()
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->with('feeType:id,name')
            ->latest()
            ->get()
            ->map(fn ($inv) => [
                'id'        => $inv->id,
                'fee_type'  => $inv->feeType?->name,
                'period'    => $inv->period ? $inv->period->format('Y-m') : null,
                'amount'    => $inv->amount,
                'status'    => $inv->status,
                'trx_id'    => $inv->trx_id,
            ]);

        return response()->json(['data' => $items]);
    }
}
