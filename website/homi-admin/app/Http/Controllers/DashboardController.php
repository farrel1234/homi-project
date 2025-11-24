<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== Statistik umum =====
        $totalResidents = DB::table('residents')->count();
        $totalUsers = DB::table('users')->count();

        $totalComplaints = DB::table('complaints')->count();
        $pendingComplaints = DB::table('complaints')
            ->where('status', 'submitted')
            ->count();

        $totalServiceRequests = DB::table('service_requests')->count();
        $activeServiceRequests = DB::table('service_requests')
            ->whereIn('status', ['submitted', 'in_progress'])
            ->count();

        $pendingPayments = DB::table('payments')
            ->where('status', 'pending')
            ->count();

        $paidPaymentsThisMonth = DB::table('payments')
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        // ===== Pengumuman terbaru =====
        $latestAnnouncements = DB::table('announcements')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ===== Pengaduan terbaru =====
        $latestComplaints = DB::table('complaints')
            ->select('id', 'title', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ===== Permohonan layanan terbaru =====
        $latestServiceRequests = DB::table('service_requests')
            ->select('id', 'title', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ===== Grafik pembayaran 6 bulan terakhir =====
        $paymentStats = DB::table('payments')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total_amount')
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(6)
            ->get();

        $paymentChartLabels = $paymentStats->pluck('month');        // ex: ["2025-05", ...]
        $paymentChartData   = $paymentStats->pluck('total_amount'); // ex: [1200000, ...]

        return view('dashboard.index', compact(
            'totalResidents',
            'totalUsers',
            'totalComplaints',
            'pendingComplaints',
            'totalServiceRequests',
            'activeServiceRequests',
            'pendingPayments',
            'paidPaymentsThisMonth',
            'latestAnnouncements',
            'latestComplaints',
            'latestServiceRequests',
            'paymentChartLabels',
            'paymentChartData'
        ));
    }
}
