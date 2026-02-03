<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status'); // pending/paid/failed (untuk UI)
        $q      = $request->input('q');

        // mapping filter UI -> kolom asli review_status
        $map = [
            'pending' => 'pending',
            'paid'    => 'approved',
            'failed'  => 'rejected',
        ];

        $query = Payment::query()
            ->with(['user', 'invoice']);

        if ($status && isset($map[$status])) {
            $query->where('review_status', $map[$status]);
        }

        if ($q) {
            $query->where(function ($payment) use ($q) {
                $payment->where('note', 'like', "%$q%")
                    ->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('full_name', 'like', "%$q%")
                           ->orWhere('name', 'like', "%$q%")
                           ->orWhere('username', 'like', "%$q%")
                           ->orWhere('email', 'like', "%$q%");
                    })
                    ->orWhereHas('invoice', function ($iq) use ($q) {
                        $iq->where('trx_id', 'like', "%$q%")
                           ->orWhere('status', 'like', "%$q%");
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
        $payment->load(['user', 'invoice', 'reviewer']);
        return view('payments.show', compact('payment'));
    }

    public function approve(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $payment->review_status = 'approved';
        $payment->reviewed_by  = auth()->id();
        $payment->reviewed_at  = now();

        if (!empty($data['reason'])) {
            $payment->note = $data['reason'];
        }

        $payment->save();

        return back()->with('success', 'Pembayaran berhasil disetujui.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $payment->review_status = 'rejected';
        $payment->reviewed_by  = auth()->id();
        $payment->reviewed_at  = now();

        if (!empty($data['reason'])) {
            $payment->note = $data['reason'];
        }

        $payment->save();

        return back()->with('success', 'Pembayaran berhasil ditolak.');
    }

    // kalau kamu memang pakai cancel di UI, kita map ke rejected (atau buat field baru)
    public function cancel(Request $request, Payment $payment)
    {
        $payment->review_status = 'rejected';
        $payment->reviewed_by  = auth()->id();
        $payment->reviewed_at  = now();
        $payment->note         = 'Dibatalkan oleh admin.';
        $payment->save();

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
                ->get();

            foreach ($payments as $payment) {
                if ($data['action'] === 'approve') {
                    $payment->review_status = 'approved';
                    $payment->note = !empty($data['reason'])
                        ? '[BULK APPROVE] '.$data['reason']
                        : '[BULK APPROVE] Disetujui oleh admin.';
                } else {
                    $payment->review_status = 'rejected';
                    $payment->note = !empty($data['reason'])
                        ? '[BULK REJECT] '.$data['reason']
                        : '[BULK REJECT] Ditolak oleh admin.';
                }

                $payment->reviewed_by = auth()->id();
                $payment->reviewed_at = now();
                $payment->save();
            }
        });

        return back()->with('success',
            $data['action'] === 'approve'
                ? 'Pembayaran terpilih berhasil di-approve.'
                : 'Pembayaran terpilih berhasil ditolak.'
        );
    }
}
