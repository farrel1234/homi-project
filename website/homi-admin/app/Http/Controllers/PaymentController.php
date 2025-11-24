<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * List pembayaran + filter.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $q      = $request->input('q');

        $query = Payment::with('user');

        if ($status) {
            $query->where('status', $status);
        }

        if ($q) {
            $query->where(function ($payment) use ($q) {
                $payment->where('description', 'like', "%$q%")
                        ->orWhereHas('user', function ($uq) use ($q) {
                            $uq->where('full_name', 'like', "%$q%")
                               ->orWhere('username', 'like', "%$q%")
                               ->orWhere('email', 'like', "%$q%");
                        });
            });
        }

        $payments = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('payments.index', compact('payments', 'status', 'q'));
    }

    /**
     * Detail 1 pembayaran.
     */
    public function show(Payment $payment)
    {
        // load relasi user (kalau Resident sudah ada relasinya boleh ditambah)
        $payment->load(['user']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Approve 1 pembayaran (ubah jadi paid).
     */
    public function approve(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $payment->status = 'paid';

        if (! $payment->paid_at) {
            $payment->paid_at = now();
        }

        if (! empty($data['reason'])) {
            $payment->admin_note = $data['reason'];
        }

        $payment->save();

        return back()->with('success', 'Pembayaran berhasil disetujui dan dinyatakan lunas.');
    }

    /**
     * Reject 1 pembayaran (tandai gagal).
     */
    public function reject(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $payment->status = 'failed';

        if (! empty($data['reason'])) {
            $payment->admin_note = $data['reason'];
        }

        // Kalau mau, bisa kosongkan paid_at:
        // $payment->paid_at = null;

        $payment->save();

        return back()->with('success', 'Pembayaran berhasil ditolak / ditandai gagal.');
    }

    /**
     * Bulk approve / reject pembayaran.
     */
    public function bulk(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'action'      => 'required|in:approve,reject',
            'selected'    => 'required|array',
            'selected.*'  => 'integer|exists:payments,id',
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
                    // Set status paid
                    $payment->status = 'paid';

                    // Isi paid_at kalau belum ada
                    if (! $payment->paid_at) {
                        $payment->paid_at = now();
                    }

                    $reason = $data['reason'] ?? 'Disetujui oleh admin (bulk).';
                    $payment->admin_note = trim('[BULK APPROVE] '.$reason);
                } else {
                    // reject => failed
                    $payment->status = 'failed';

                    $reason = $data['reason'] ?? 'Ditolak oleh admin (bulk).';
                    $payment->admin_note = trim('[BULK REJECT] '.$reason);
                }

                $payment->save();
            }
        });

        $msg = $data['action'] === 'approve'
            ? 'Pembayaran terpilih berhasil di-approve.'
            : 'Pembayaran terpilih berhasil ditolak (FAILED).';

        return back()->with('success', $msg);
    }
}
