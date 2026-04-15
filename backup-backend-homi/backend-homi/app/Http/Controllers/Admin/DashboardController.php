<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Announcement;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Models\FeeInvoice;
use App\Models\Complaint;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ========== LOGIK KHUSUS SUPER ADMIN ==========
        if ($user->isSuperAdmin()) {
            $totalTenants = \App\Models\Tenant::count();
            $activeTenants = \App\Models\Tenant::where('is_active', true)->count();
            $pendingRequests = \App\Models\TenantRequest::where('status', 'pending')->count();
            
            $recentRequests = \App\Models\TenantRequest::orderByDesc('created_at')->take(5)->get();
            $recentTenants  = \App\Models\Tenant::orderByDesc('created_at')->take(5)->get();

            return view('dashboard.super', compact(
                'totalTenants',
                'activeTenants',
                'pendingRequests',
                'recentRequests',
                'recentTenants'
            ));
        }

        // ========== LOGIK TENANT ADMIN (DEFAULT) ==========
        // Parameter tunggakan
        $atRiskDays = 14;
        $highRiskDays = 30;

        // ========== CARD RINGKASAN ATAS ==========

        // Total warga
        $totalResidents = Resident::count();

        // Total pengumuman
        $totalAnnouncements = Announcement::count();

        // Pembayaran pending (review_status)
        $pendingPaymentsCount = Payment::where('review_status', 'pending')->count();

        // Total nominal pending (ambil dari fee_invoices.amount)
        $totalPendingAmount = Payment::query()
            ->where('review_status', 'pending')
            ->leftJoin('fee_invoices', 'fee_payments.invoice_id', '=', 'fee_invoices.id')
            ->sum('fee_invoices.amount');

        // Pengajuan layanan
        $serviceRequestCount  = ServiceRequest::count();
        $serviceRequestsToday = ServiceRequest::whereDate('created_at', now()->toDateString())->count();

        // Pengaduan warga
        $complaintCount = Complaint::count();
        $complaintsToday = Complaint::whereDate('created_at', now()->toDateString())->count();
        $pendingComplaintsCount = Complaint::where('status', 'baru')->count();

        // ========== PENGUMUMAN UTAMA + LAINNYA ==========

        $mainAnnouncement = Announcement::orderByDesc('created_at')->first();

        $nextAnnouncements = Announcement::when($mainAnnouncement, function ($q) use ($mainAnnouncement) {
                $q->where('id', '!=', $mainAnnouncement->id);
            })
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        // ========== PEMBAYARAN TERBARU ==========
        $latestPayments = Payment::with(['user', 'invoice'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // ========== CHART 1: TOTAL APPROVED 6 BULAN TERAKHIR ==========
        $start = now()->subMonths(5)->startOfMonth();
        $end   = now()->endOfMonth();

        $rawMonthly = Payment::query()
            ->where('review_status', 'approved')
            ->whereBetween('fee_payments.created_at', [$start, $end])
            ->leftJoin('fee_invoices', 'fee_payments.invoice_id', '=', 'fee_invoices.id')
            ->selectRaw('DATE_FORMAT(fee_payments.created_at, "%Y-%m") as ym, COALESCE(SUM(fee_invoices.amount),0) as total')
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym'); // ['2025-09' => 3000000, ...]

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

        // ========== CHART 2: KOMPOSISI NOMINAL PER REVIEW STATUS ==========
        $statusMap = [
            'approved' => 'Disetujui',
            'pending'  => 'Menunggu',
            'rejected' => 'Ditolak',
        ];

        $statusRaw = Payment::query()
            ->leftJoin('fee_invoices', 'fee_payments.invoice_id', '=', 'fee_invoices.id')
            ->selectRaw('fee_payments.review_status as st, COALESCE(SUM(fee_invoices.amount),0) as total')
            ->groupBy('st')
            ->pluck('total', 'st'); // ['approved' => 5000000, 'pending' => 1500000, ...]

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

        // ========== DATA TUNGGAKAN (ARREARS SUMMARY) ==========
        $unpaidInvoices = FeeInvoice::query()
            ->where('status', 'unpaid')
            ->where('due_date', '<', now())
            ->get();

        $atRiskCount = 0;
        $highRiskCount = 0;
        $totalArrearsAmount = 0;
        $userArrears = [];

        foreach ($unpaidInvoices as $inv) {
            $daysOverdue = now()->diffInDays($inv->due_date);
            $totalArrearsAmount += $inv->amount;

            if ($daysOverdue >= $highRiskDays) {
                $highRiskCount++;
            } elseif ($daysOverdue >= $atRiskDays) {
                $atRiskCount++;
            }

            if (!isset($userArrears[$inv->user_id])) {
                $userArrears[$inv->user_id] = [
                    'name' => $inv->user->full_name ?? $inv->user->name ?? 'Warga',
                    'blok' => $inv->user->residentProfile->blok ?? '-',
                    'no_rumah' => $inv->user->residentProfile->no_rumah ?? '-',
                    'amount' => 0,
                    'days_overdue' => 0,
                    'action_url' => route('admin.fees.invoices.index', ['user_id' => $inv->user_id])
                ];
            }

            $userArrears[$inv->user_id]['amount'] += $inv->amount;
            $userArrears[$inv->user_id]['days_overdue'] = max($userArrears[$inv->user_id]['days_overdue'], $daysOverdue);
        }

        // Sort by amount descending and take top 5
        usort($userArrears, fn($a, $b) => $b['amount'] <=> $a['amount']);
        $topArrears = array_slice($userArrears, 0, 5);

        $arrearsSummary = [
            'at_risk_count' => $atRiskCount,
            'high_risk_count' => $highRiskCount,
            'at_risk_days' => $atRiskDays,
            'high_risk_days' => $highRiskDays,
            'total_arrears_amount' => $totalArrearsAmount,
            'top_arrears' => $topArrears,
            'cta_url' => route('admin.notifications.create', ['type' => 'arrears_warning']),
            'cta_label' => 'Kirim Pengingat'
        ];

        // ========== KIRIM KE VIEW ==========
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
            'arrearsSummary'
        ));
    }
}
