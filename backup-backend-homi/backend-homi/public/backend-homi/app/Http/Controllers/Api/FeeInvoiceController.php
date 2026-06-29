<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeeInvoiceController extends Controller
{
    /**
     * ADMIN: buat tagihan (bisa massal)
     * JSON:
     * - fee_type_id
     * - period (contoh 2025-08-01)
     * - amount
     * - due_date (optional)
     * - scope: all|users
     * - user_ids (kalau scope=users)
     */
    public function adminCreate(Request $request)
    {
        $request->validate([
            'fee_type_id' => 'required|exists:fee_types,id',
            'period'      => 'required|date',
            'amount'      => 'required|integer|min:0',
            'due_date'    => 'nullable|date',
            'scope'       => 'required|in:all,users',
            'user_ids'    => 'nullable|array',
            'user_ids.*'  => 'integer|exists:users,id',
        ]);

        // normalisasi period jadi awal bulan
        $period = date('Y-m-01', strtotime($request->input('period')));

        $usersQuery = User::query()->where('role', 'resident');

        if ($request->input('scope') === 'users') {
            $ids = $request->input('user_ids', []);
            $usersQuery->whereIn('id', $ids);
        }

        $users = $usersQuery->get(['id']);

        $created = 0;
        $skipped = 0;

        foreach ($users as $u) {
            $exists = FeeInvoice::query()
                ->where('user_id', $u->id)
                ->where('fee_type_id', $request->fee_type_id)
                ->where('period', $period)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            FeeInvoice::create([
                'user_id'     => $u->id,
                'fee_type_id' => $request->fee_type_id,
                'period'      => $period,
                'amount'      => $request->amount,
                'status'      => 'unpaid',
                'trx_id'      => 'IPL-' . strtoupper(Str::random(10)),
                'issued_by'   => $request->user()->id,
                'due_date'    => $request->input('due_date'),
            ]);

            $created++;
        }

        return response()->json([
            'message' => 'Invoices generated',
            'created' => $created,
            'skipped' => $skipped,
        ]);
    }

    /**
     * ADMIN: list tagihan (filter status/period)
     * Query:
     * - status=unpaid|pending|paid|rejected
     * - period=2025-08-01
     */
    public function adminIndex(Request $request)
    {
        $qStatus = $request->query('status');
        $qPeriod = $request->query('period');

        $data = FeeInvoice::query()
            ->with(['user:id,name,email', 'feeType:id,name'])
            ->when($qStatus, fn($q) => $q->where('status', $qStatus))
            ->when($qPeriod, fn($q) => $q->where('period', date('Y-m-01', strtotime($qPeriod))))
            ->latest()
            ->paginate(20);

        return response()->json($data);
    }

    /**
     * WARGA: list tagihan miliknya
     * Query:
     * - status=unpaid,pending (optional)
     */
    public function residentIndex(Request $request)
    {
        $user = $request->user();

        $status = $request->query('status'); // unpaid,pending
        $statuses = $status ? array_map('trim', explode(',', $status)) : null;

        $query = FeeInvoice::query()
            ->where('user_id', $user->id)
            ->with('feeType:id,name')
            ->latest();

        if ($statuses) {
            $query->whereIn('status', $statuses);
        }

        $items = $query->get()->map(fn ($inv) => [
            'id'        => $inv->id,
            'fee_type'  => $inv->feeType?->name,
            'amount'    => $inv->amount,
            'status'    => $inv->status,
            'trx_id'    => $inv->trx_id,
            'period'    => $inv->period ? $inv->period->format('Y-m') : null,
            'due_date'  => $inv->due_date ? $inv->due_date->format('Y-m-d') : null,
        ]);

        return response()->json(['data' => $items]);
    }

}
