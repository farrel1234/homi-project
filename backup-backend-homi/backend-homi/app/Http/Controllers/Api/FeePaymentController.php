<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
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

            // update invoice status (pakai assignment biar aman dari fillable issue)
            $invoice->status = 'pending';
            $invoice->save();

            return [
                'payment' => $payment,
            ];
        });

        return response()->json([
            'message' => 'Proof uploaded, waiting for admin review',
            'data' => [
                'payment_id' => $result['payment']->id,
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
