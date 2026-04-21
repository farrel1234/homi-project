<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Announcement;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Models\FeeInvoice;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        =====================================
        SUPER ADMIN
        =====================================
        */
        if ($user->isSuperAdmin()) {

            $totalTenants    = \App\Models\Tenant::count();
            $activeTenants   = \App\Models\Tenant::where('is_active', true)->count();
            $pendingRequests = \App\Models\TenantRequest::where('status', 'pending')->count();

            $recentRequests = \App\Models\TenantRequest::latest()->take(5)->get();
            $recentTenants  = \App\Models\Tenant::latest()->take(5)->get();

            return view('dashboard.super', compact(
                'totalTenants',
                'activeTenants',
                'pendingRequests',
                'recentRequests',
                'recentTenants'
            ));
        }

        /*
        =====================================
        DASHBOARD TENANT
        =====================================
        */

        $atRiskDays   = 14;
        $highRiskDays = 30;

        /*
        =====================================
        CARD ATAS
        =====================================
        */

        $totalResidents = Resident::count();
        $totalAnnouncements = Announcement::count();

        $pendingPaymentsCount = Payment::where('review_status', 'pending')->count();

        $totalPendingAmount = Payment::query()
            ->where('review_status', 'pending')
            ->leftJoin('fee_invoices', 'fee_payments.invoice_id', '=', 'fee_invoices.id')
            ->sum('fee_invoices.amount');

        $serviceRequestCount = ServiceRequest::count();

        $serviceRequestsToday = ServiceRequest::whereDate(
            'created_at',
            now()->toDateString()
        )->count();

        $complaintCount = Complaint::count();

        $complaintsToday = Complaint::whereDate(
            'created_at',
            now()->toDateString()
        )->count();

        $pendingComplaintsCount = Complaint::where('status', 'baru')->count();

        /*
        =====================================
        PENGUMUMAN
        =====================================
        */

        $mainAnnouncement = Announcement::latest()->first();

        $nextAnnouncements = Announcement::when(
            $mainAnnouncement,
            fn($q) => $q->where('id', '!=', $mainAnnouncement->id)
        )
        ->latest()
        ->take(3)
        ->get();

        /*
        =====================================
        PAYMENT TERBARU
        =====================================
        */

        $latestPayments = Payment::with(['user', 'invoice'])
            ->latest()
            ->take(5)
            ->get();

        /*
        =====================================
        CHART BULANAN
        =====================================
        */

        $start = now()->subMonths(5)->startOfMonth();
        $end   = now()->endOfMonth();

        $rawMonthly = Payment::query()
            ->where('review_status', 'approved')
            ->whereBetween('fee_payments.created_at', [$start, $end])
            ->leftJoin('fee_invoices', 'fee_payments.invoice_id', '=', 'fee_invoices.id')
            ->selectRaw('DATE_FORMAT(fee_payments.created_at,"%Y-%m") as ym')
            ->selectRaw('COALESCE(SUM(fee_invoices.amount),0) as total')
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $monthlyLabels = [];
        $monthlyData   = [];

        for ($i = 5; $i >= 0; $i--) {

            $month = now()->subMonths($i)->startOfMonth();
            $ym    = $month->format('Y-m');

            $monthlyLabels[] = $month->translatedFormat('M Y');
            $monthlyData[]   = (int) ($rawMonthly[$ym] ?? 0);
        }

        $chartMonthly = [
            'labels' => $monthlyLabels,
            'data'   => $monthlyData,
        ];

        /*
        =====================================
        CHART STATUS
        =====================================
        */

        $statusMap = [
            'approved' => 'Disetujui',
            'pending'  => 'Menunggu',
            'rejected' => 'Ditolak',
        ];

        $statusRaw = Payment::query()
            ->leftJoin('fee_invoices', 'fee_payments.invoice_id', '=', 'fee_invoices.id')
            ->selectRaw('fee_payments.review_status as st')
            ->selectRaw('COALESCE(SUM(fee_invoices.amount),0) as total')
            ->groupBy('st')
            ->pluck('total', 'st');

        $statusLabels = [];
        $statusData   = [];

        foreach ($statusMap as $key => $label) {
            $statusLabels[] = $label;
            $statusData[]   = (int) ($statusRaw[$key] ?? 0);
        }

        $chartStatus = [
            'labels' => $statusLabels,
            'data'   => $statusData,
        ];

        /*
        =====================================
        RISIKO TUNGGAKAN
        =====================================
        */

        $unpaidInvoices = FeeInvoice::query()
            ->where('status', 'unpaid')
            ->get();

        $atRiskCount = 0;
        $highRiskCount = 0;
        $totalArrearsAmount = 0;

        foreach ($unpaidInvoices as $inv) {

            $daysOverdue = now()->diffInDays($inv->period);
            $totalArrearsAmount += $inv->amount;

            if ($daysOverdue >= $highRiskDays) {
                $highRiskCount++;
            } elseif ($daysOverdue >= $atRiskDays) {
                $atRiskCount++;
            }
        }

        $arrearsSummary = [
            'at_risk_count'        => $atRiskCount,
            'high_risk_count'      => $highRiskCount,
            'total_arrears_amount' => $totalArrearsAmount,
            'top_arrears'          => [],
            'cta_url'              => route('admin.prioritas-tunggakan'),
            'cta_label'            => 'Lihat Detail'
        ];

        /*
        =====================================
        PRIORITAS TUNGGAKAN (SAW)
        =====================================
        */

        $rows = DB::table('fee_invoices')
            ->join('users', 'users.id', '=', 'fee_invoices.user_id')
            ->join('fee_types', 'fee_types.id', '=', 'fee_invoices.fee_type_id')
            ->select(
                'users.name',
                DB::raw('SUM(fee_invoices.amount) as total_tunggakan'),
                DB::raw('COUNT(*) as jumlah_bulan'),
                DB::raw('COUNT(DISTINCT fee_invoices.fee_type_id) as jenis_tunggakan')
            )
            ->where('fee_invoices.status', 'unpaid')
            ->groupBy('users.id', 'users.name')
            ->get();

        $prioritySummary = [
            'high'   => 0,
            'medium' => 0,
            'low'    => 0,
            'total'  => 0
        ];

        if ($rows->count() > 0) {

            $maxTotal = $rows->max('total_tunggakan');
            $maxBulan = $rows->max('jumlah_bulan');
            $maxJenis = $rows->max('jenis_tunggakan');

            foreach ($rows as $row) {

                $n1 = $maxTotal > 0 ? $row->total_tunggakan / $maxTotal : 0;
                $n2 = $maxBulan > 0 ? $row->jumlah_bulan / $maxBulan : 0;
                $n3 = $maxJenis > 0 ? $row->jenis_tunggakan / $maxJenis : 0;

                $skor = ($n1 * 0.50) + ($n2 * 0.35) + ($n3 * 0.15);

                if ($skor >= 0.80) {
                    $prioritySummary['high']++;
                } elseif ($skor >= 0.60) {
                    $prioritySummary['medium']++;
                } else {
                    $prioritySummary['low']++;
                }

                $prioritySummary['total'] += $row->total_tunggakan;
            }
        }

        /*
        =====================================
        RETURN VIEW
        =====================================
        */

        return view('dashboard.index', compact(
            'totalResidents',
            'totalAnnouncements',
            'pendingPaymentsCount',
            'totalPendingAmount',
            'serviceRequestCount',
            'serviceRequestsToday',
            'complaintCount',
            'complaintsToday',
            'pendingComplaintsCount',
            'mainAnnouncement',
            'nextAnnouncements',
            'latestPayments',
            'chartMonthly',
            'chartStatus',
            'arrearsSummary',
            'prioritySummary'
        ));
    }
}