<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentApiController extends Controller
{
    public function index(Request $request)
    {
        $items = Payment::orderByDesc('id')->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
            ],
        ]);
    }

    public function store(Request $request)
{
    return response()->json([
        'debug' => 'request diterima',
        'headers' => $request->headers->all(),
        'fields' => $request->all(),
        'hasFile_proof' => $request->hasFile('proof'),
        'content_type' => $request->header('Content-Type'),
    ]);

        try {
            $nextId    = (Payment::max('id') ?? 0) + 1;
            $invoiceId = 'INV-'.str_pad((string)$nextId, 3, '0', STR_PAD_LEFT);

            $path = null;
            if ($request->hasFile('proof')) {
                $path = $request->file('proof')->store('bukti', 'public');
            }

            $payment = Payment::create([
                'invoice_id'    => $invoiceId,
                'resident_name' => $validated['resident_name'],
                'unit'          => $validated['unit'],
                'period'        => $validated['period'],
                'amount'        => $validated['amount'],
                'method'        => $validated['method'] ?? null,
                'proof_path'    => $path,
                'status'        => 'Menunggu',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran diterima. Menunggu verifikasi admin.',
                'data'    => $payment,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('PAYMENT_STORE_FAILED: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan pembayaran.',
            ], 500);
        }
    }
}
