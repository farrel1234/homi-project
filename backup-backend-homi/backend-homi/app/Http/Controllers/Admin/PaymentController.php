<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment as Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // UI filter: pending / paid / failed
        $status = $request->input('status');
        $q      = $request->input('q');

        // mapping filter UI -> review_status di fee_payments
        $map = [
            'pending' => 'pending',
            'paid'    => 'approved',
            'failed'  => 'rejected',
        ];

        $query = Payment::query()->with([
            'payer:id,full_name,name,username,email,phone',
            'invoice.feeType:id,name',
            'reviewer:id,name,full_name,username,email',
        ]);

        if ($status && isset($map[$status])) {
            $query->where('review_status', $map[$status]);
        }

        if ($q) {
            $query->where(function ($payment) use ($q) {
                $payment->where('note', 'like', "%$q%")
                    ->orWhereHas('payer', function ($uq) use ($q) {
                        $uq->where('full_name', 'like', "%$q%")
                           ->orWhere('name', 'like', "%$q%")
                           ->orWhere('username', 'like', "%$q%")
                           ->orWhere('email', 'like', "%$q%");
                    })
                    ->orWhereHas('invoice', function ($iq) use ($q) {
                        $iq->where('trx_id', 'like', "%$q%")
                           ->orWhere('status', 'like', "%$q%");
                    })
                    ->orWhereHas('invoice.feeType', function ($fq) use ($q) {
                        $fq->where('name', 'like', "%$q%");
                    });
            });
        }

        $payments = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('payments.index', compact('payments', 'status', 'q'));
    }

    public function show(Payment $payment)
    {
        $payment->load([
            'payer:id,full_name,name,username,email,phone',
            'invoice.feeType:id,name',
            'reviewer:id,name,full_name,username,email',
        ]);

        $invoice = $payment->invoice;
        $feeName = $invoice->feeType->name ?? 'Iuran';
        $periodText = $invoice->period ? $invoice->period->format('M Y') : '-';
        $amount = $invoice->total_amount ?? $invoice->amount ?? 0;
        $dueDate = $invoice->due_date;
        $payer = $payment->payer;
        $name = $payer->full_name ?? $payer->name ?? 'Warga';

        return view('payments.show', compact('payment', 'feeName', 'periodText', 'amount', 'dueDate', 'payer', 'name'));
    }

    public function approve(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($data, $payment) {
            $payment->review_status = 'approved';
            $payment->reviewed_by  = auth()->id();
            $payment->reviewed_at  = now();

            // ✅ ALWAYS overwrite note (biar nggak nyisa note lama)
            $payment->note = !empty($data['reason'])
                ? $data['reason']
                : 'Disetujui oleh admin.';

            $payment->save();

            // ✅ Sinkron status invoice
            if ($payment->invoice) {
                $payment->invoice->update([
                    'status' => 'paid',
                ]);
            }
        });

        return back()->with('success', 'Pembayaran berhasil disetujui.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($data, $payment) {
            $payment->review_status = 'rejected';
            $payment->reviewed_by  = auth()->id();
            $payment->reviewed_at  = now();

            // ✅ ALWAYS overwrite note
            $payment->note = !empty($data['reason'])
                ? $data['reason']
                : 'Ditolak oleh admin.';

            $payment->save();

            // ✅ Sinkron status invoice
            if ($payment->invoice) {
                $payment->invoice->update([
                    'status' => 'rejected',
                ]);
            }
        });

        return back()->with('success', 'Pembayaran berhasil ditolak.');
    }

    public function cancel(Request $request, Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $payment->review_status = 'rejected';
            $payment->reviewed_by  = auth()->id();
            $payment->reviewed_at  = now();
            $payment->note         = 'Dibatalkan oleh admin.';
            $payment->save();

            if ($payment->invoice) {
                $payment->invoice->update([
                    'status' => 'rejected',
                ]);
            }
        });

        return back()->with('success', 'Pembayaran dibatalkan.');
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action'      => 'required|in:approve,reject',
            'selected'    => 'required|array',
            'selected.*'  => 'integer|exists:fee_payments,id',
            'reason'      => 'nullable|string|max:500',
        ], [
            'selected.required' => 'Pilih minimal satu pembayaran terlebih dahulu.',
        ]);

        DB::transaction(function () use ($data) {
            $payments = Payment::whereIn('id', $data['selected'])
                ->lockForUpdate()
                ->with('invoice')
                ->get();

            foreach ($payments as $payment) {
                if ($data['action'] === 'approve') {
                    $payment->review_status = 'approved';
                    $payment->note = !empty($data['reason'])
                        ? '[BULK APPROVE] ' . $data['reason']
                        : '[BULK APPROVE] Disetujui oleh admin.';

                    if ($payment->invoice) {
                        $payment->invoice->update(['status' => 'paid']);
                    }
                } else {
                    $payment->review_status = 'rejected';
                    $payment->note = !empty($data['reason'])
                        ? '[BULK REJECT] ' . $data['reason']
                        : '[BULK REJECT] Ditolak oleh admin.';

                    if ($payment->invoice) {
                        $payment->invoice->update(['status' => 'rejected']);
                    }
                }

                $payment->reviewed_by = auth()->id();
                $payment->reviewed_at = now();
                $payment->save();
            }
        });

        return back()->with(
            'success',
            $data['action'] === 'approve'
                ? 'Pembayaran terpilih berhasil di-approve.'
                : 'Pembayaran terpilih berhasil ditolak.'
        );
    }
}
