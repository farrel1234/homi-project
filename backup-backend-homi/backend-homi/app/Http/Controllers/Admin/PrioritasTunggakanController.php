<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrioritasTunggakanController extends Controller
{
    public function index(Request $request)
    {
        $tahun      = $request->tahun;
        $bulanAwal  = $request->bulan_awal;
        $bulanAkhir = $request->bulan_akhir;
        $jenis      = $request->jenis;

        $query = DB::table('fee_invoices')
            ->join('users', 'users.id', '=', 'fee_invoices.user_id')
            ->join('fee_types', 'fee_types.id', '=', 'fee_invoices.fee_type_id')
            ->select(
                'users.name',
                DB::raw('SUM(fee_invoices.amount) as total_tunggakan'),
                DB::raw('COUNT(*) as jumlah_bulan'),
                DB::raw('COUNT(DISTINCT fee_invoices.fee_type_id) as jenis_tunggakan'),
                DB::raw('MIN(fee_invoices.period) as awal'),
                DB::raw('MAX(fee_invoices.period) as akhir')
            )
            ->where('fee_invoices.status', 'unpaid');

        /*
        ========================
        FILTER JENIS TAGIHAN
        ========================
        */
        if ($jenis && $jenis != 'all') {
            $query->where('fee_types.name', $jenis);
        }

        /*
        ========================
        FILTER PERIODE
        ========================
        */
        if ($tahun == 'all' && $bulanAwal && $bulanAkhir) {

            $query->whereMonth('fee_invoices.period', '>=', $bulanAwal)
                  ->whereMonth('fee_invoices.period', '<=', $bulanAkhir);

        } elseif ($tahun && $tahun != 'all' && $bulanAwal && $bulanAkhir) {

            $tanggalAwal = $tahun . '-' . str_pad($bulanAwal, 2, '0', STR_PAD_LEFT) . '-01';

            $tanggalAkhir = date(
                'Y-m-t',
                strtotime($tahun . '-' . str_pad($bulanAkhir, 2, '0', STR_PAD_LEFT) . '-01')
            );

            $query->whereBetween('fee_invoices.period', [$tanggalAwal, $tanggalAkhir]);
        }

        $rows = $query
            ->groupBy('users.id', 'users.name')
            ->get();

        if ($rows->count() == 0) {
            return view('admin.prioritas-tunggakan', ['data' => []]);
        }

        /*
        ========================
        MAX NILAI NORMALISASI
        ========================
        */
        $maxTotal = $rows->max('total_tunggakan');
        $maxBulan = $rows->max('jumlah_bulan');
        $maxJenis = $rows->max('jenis_tunggakan');

        $data = [];

        foreach ($rows as $row) {

            $n1 = $maxTotal > 0 ? $row->total_tunggakan / $maxTotal : 0;
            $n2 = $maxBulan > 0 ? $row->jumlah_bulan / $maxBulan : 0;
            $n3 = $maxJenis > 0 ? $row->jenis_tunggakan / $maxJenis : 0;

            /*
            ========================
            BOBOT SAW
            ========================
            */
            $skor = ($n1 * 0.50) + ($n2 * 0.35) + ($n3 * 0.15);

            if ($skor >= 0.80) {
                $prioritas = 'Tinggi';
            } elseif ($skor >= 0.60) {
                $prioritas = 'Sedang';
            } else {
                $prioritas = 'Rendah';
            }

            $awal  = Carbon::parse($row->awal)->translatedFormat('M Y');
            $akhir = Carbon::parse($row->akhir)->translatedFormat('M Y');

            $periodeText = $row->jumlah_bulan . ' Bulan (' . $awal . ' - ' . $akhir . ')';

            $data[] = [
                'nama'       => $row->name,
                'tunggakan'  => $row->total_tunggakan,
                'bulan'      => $periodeText,
                'jenis'      => $row->jenis_tunggakan,
                'skor'       => round($skor, 2),
                'prioritas'  => $prioritas
            ];
        }

        usort($data, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        return view('admin.prioritas-tunggakan', compact('data'));
    }
}