<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Announcement;
use App\Models\Payment;
use App\Models\LetterRequest;

class DashboardController extends Controller
{
    public function index()
    {
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
        $serviceRequestCount  = LetterRequest::count();
        $serviceRequestsToday = LetterRequest::whereDate('created_at', now()->toDateString())->count();

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

        // ========== KIRIM KE VIEW ==========
        return view('dashboard.index', compact(
            'totalResidents',
            'totalAnnouncements',
            'pendingPaymentsCount',
            'totalPendingAmount',
            'serviceRequestCount',
            'serviceRequestsToday',
            'mainAnnouncement',
            'nextAnnouncements',
            'latestPayments',
            'chartMonthly',
            'chartStatus',
        ));
    }
}
