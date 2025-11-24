<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class QuickPaymentValidationController extends Controller
{
    /** Ambil daftar status distinct yang ada di DB (lowercase) */
    protected function existingStatuses(): array
    {
        if (!Schema::hasTable('payments') || !Schema::hasColumn('payments', 'status')) return [];
        try {
            return DB::table('payments')->select('status')->distinct()
                ->pluck('status')->filter()->map(fn($s)=>mb_strtolower(trim($s)))->values()->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Pilih nilai status yang paling cocok dengan target (‘paid’, ‘failed’, ‘pending’, ‘cancelled’) */
    protected function pickStatus(string $target): string
    {
        $target = mb_strtolower($target);
        $aliases = [
            'paid'      => ['paid','lunas','success','berhasil','paid_payment'],
            'pending'   => ['pending','menunggu','pending_payment'],
            'failed'    => ['failed','gagal','ditolak','failed_payment'],
            'cancelled' => ['cancelled','dibatalkan','batal','cancel'],
        ];

        $existing = $this->existingStatuses(); // ex: ['menunggu','lunas', ...]
        // Kalau sudah ada nilai target persis di DB, pakai yang itu (case-insensitive)
        foreach ($existing as $ex) {
            if (in_array($ex, $aliases[$target] ?? [], true)) {
                // kembalikan versi EXACT yang ada di DB (supaya konsisten)
                return $ex;
            }
        }
        // Kalau nggak ada, pakai default Inggris biar stabil
        return $target;
    }

    public function approve(Request $request, int $id)
    {
        // Lock baris agar aman dari race condition
        return DB::transaction(function() use ($request, $id) {
            $row = DB::table('payments')->lockForUpdate()->where('id',$id)->first();
            if (!$row) return back()->with('error','Payment tidak ditemukan.');

            $targetStatus = $this->pickStatus('paid');

            $data = ['status' => $targetStatus];
            if (Schema::hasColumn('payments','paid_at'))  $data['paid_at'] = Carbon::now();
            if (Schema::hasColumn('payments','updated_at')) $data['updated_at'] = Carbon::now();
            if (Schema::hasColumn('payments','notes') && $request->filled('notes')) {
                $data['notes'] = $request->string('notes');
            }

            DB::table('payments')->where('id',$id)->update($data);
            return back()->with('ok','Pembayaran disetujui.');
        });
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        return DB::transaction(function() use ($request, $id) {
            $row = DB::table('payments')->lockForUpdate()->where('id',$id)->first();
            if (!$row) return back()->with('error','Payment tidak ditemukan.');

            $targetStatus = $this->pickStatus('failed');

            $data = ['status' => $targetStatus];
            // Kalau ada paid_at, biasanya dikosongkan saat reject
            if (Schema::hasColumn('payments','paid_at'))  $data['paid_at'] = null;
            if (Schema::hasColumn('payments','updated_at')) $data['updated_at'] = Carbon::now();
            if (Schema::hasColumn('payments','notes')) {
                $data['notes'] = 'Rejected: '.$request->string('reason');
            }

            DB::table('payments')->where('id',$id)->update($data);
            return back()->with('ok','Pembayaran ditolak.');
        });
    }
}
