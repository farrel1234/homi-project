<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\FeeType;
use App\Models\PaymentQrCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FeeInvoiceController extends Controller
{
    private array $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $q = FeeInvoice::query()->with(['user', 'feeType']);

        // ✅ filter periode (kolom period = DATE/DATETIME)
        $year  = $request->integer('year');
        $month = $request->integer('month');

        if ($year)  $q->whereYear('period', $year);
        if ($month) $q->whereMonth('period', $month);

        // optional filter status
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        // ✅ urut berdasarkan period terbaru, biar grouping rapi
        $items = $q->orderByDesc('period')->orderByDesc('id')->paginate(50)->withQueryString();

        $monthNames = $this->monthNames;

        return view('admin.fees.invoices.index', compact('items', 'monthNames', 'year', 'month'));
    }

    public function create()
    {
        $feeTypes   = FeeType::query()->orderBy('id')->get();
        $users      = User::query()->orderBy('id')->limit(300)->get();
        $qr         = $this->activeQr();
        $monthNames = $this->monthNames;

        return view('admin.fees.invoices.create', compact('feeTypes', 'users', 'qr', 'monthNames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fee_type_id'   => ['required', 'integer'],
            'tahun'         => ['required', 'integer', 'min:2000', 'max:2100'],
            'bulan_mulai'   => ['required', 'integer', 'min:1', 'max:12'],
            'bulan_sampai'  => ['required', 'integer', 'min:1', 'max:12'],
            'amount'        => ['required', 'integer', 'min:1'],
            'target'        => ['required', 'in:all,one'],
            'user_id'       => ['nullable', 'integer'],
        ]);

        $bulanMulai  = (int) $request->bulan_mulai;
        $bulanSampai = (int) $request->bulan_sampai;
        $tahun       = (int) $request->tahun;

        if ($bulanSampai < $bulanMulai) {
            return back()->with('error', 'Bulan sampai harus >= bulan mulai.');
        }

        $qr = $this->activeQr();
        if (!$qr || !$qr->display_url) {
            return back()->with('error', 'QR aktif belum ada / URL QR kosong. Buat & aktifkan QR dulu.');
        }

        // target users
        if ($request->target === 'one') {
            if (!$request->user_id) return back()->with('error', 'Pilih user jika target = satu warga.');
            $targets = User::query()->where('id', $request->user_id)->get();
        } else {
            $q = User::query();
            if (Schema::hasColumn('users', 'role')) $q->where('role', 'resident');
            if (Schema::hasColumn('users', 'is_active')) $q->where('is_active', 1);
            $targets = $q->get();
        }

        if ($targets->isEmpty()) return back()->with('error', 'Tidak ada target warga.');

        $created = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($targets as $u) {
                for ($m = $bulanMulai; $m <= $bulanSampai; $m++) {
                    $period = sprintf('%04d-%02d-01', $tahun, $m); // ✅ DATE valid

                    $exists = FeeInvoice::query()
                        ->where('user_id', $u->id)
                        ->where('fee_type_id', (int) $request->fee_type_id)
                        ->where('period', $period)
                        ->exists();

                    if ($exists) { $skipped++; continue; }

                    $data = [
                        'user_id'     => $u->id,
                        'fee_type_id' => (int) $request->fee_type_id,
                        'amount'      => (int) $request->amount,
                        'status'      => 'unpaid',
                        'period'      => $period,
                        'bulan'       => $this->monthNames[$m] ?? (string) $m,
                        'tahun'       => $tahun,
                        'qr_url'      => $qr->display_url,
                    ];

                    $data = $this->filterColumns('fee_invoices', $data);
                    FeeInvoice::query()->create($data);
                    $created++;
                }
            }

            DB::commit();

            $msg = "Tagihan berhasil dibuat. Dibuat: {$created}";
            if ($skipped > 0) $msg .= " | Dilewati (sudah ada): {$skipped}";

            return redirect()->route('admin.fees.invoices.index')->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal buat tagihan: ' . $e->getMessage());
        }
    }

    private function activeQr(): ?PaymentQrCode
    {
        $table = 'payment_qr_codes';

        $activeCol = null;
        if (Schema::hasColumn($table, 'is_active')) $activeCol = 'is_active';
        else if (Schema::hasColumn($table, 'active')) $activeCol = 'active';

        if ($activeCol) {
            return PaymentQrCode::query()->where($activeCol, 1)->latest('id')->first();
        }
        return PaymentQrCode::query()->latest('id')->first();
    }

    private function filterColumns(string $table, array $data): array
    {
        foreach (array_keys($data) as $col) {
            if (!Schema::hasColumn($table, $col)) unset($data[$col]);
        }
        return $data;
    }
}
