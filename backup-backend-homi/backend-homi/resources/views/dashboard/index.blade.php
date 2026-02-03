@extends('layouts.app')

@section('title','Dashboard')

@section('page_title','Dashboard')
@section('page_subtitle','Panel Admin HOMI')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">

    {{-- JUDUL HALAMAN --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">
            Ringkasan aktivitas warga dan pengumuman terbaru.
        </p>
    </div>

    {{-- ====== CARD RINGKASAN ====== --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

        {{-- Total Warga --}}
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-500 mb-1">Total Warga Terdaftar</p>
            <p class="text-3xl font-semibold text-gray-900">
                {{ $totalResidents ?? 0 }}
            </p>
        </div>

        {{-- Total Pengumuman --}}
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-500 mb-1">Total Pengumuman</p>
            <p class="text-3xl font-semibold text-gray-900">
                {{ $totalAnnouncements ?? 0 }}
            </p>

            @if(\Illuminate\Support\Facades\Route::has('admin.announcements.index'))
                <a href="{{ route('admin.announcements.index') }}"
                   class="inline-block mt-2 text-xs text-[var(--homi-blue)] hover:underline font-medium">
                    Lihat semua →
                </a>
            @endif
        </div>

        {{-- Pembayaran Pending --}}
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-500 mb-1">Pembayaran Menunggu Diproses</p>
            <p class="text-3xl font-semibold text-gray-900">
                {{ $pendingPaymentsCount ?? 0 }}
            </p>
            <p class="text-[11px] text-gray-500 mt-1">
                Total pending:
                <span class="font-semibold text-orange-500">
                    Rp {{ number_format($totalPendingAmount ?? 0, 0, ',', '.') }}
                </span>
            </p>

            @if(\Illuminate\Support\Facades\Route::has('admin.payments.index'))
                <a href="{{ route('admin.payments.index') }}"
                   class="inline-block mt-2 text-xs text-[var(--homi-blue)] hover:underline font-medium">
                    Ke pembayaran →
                </a>
            @endif
        </div>

        {{-- Pengajuan Layanan --}}
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-500 mb-1">Pengajuan Layanan</p>
            <p class="text-3xl font-semibold text-gray-900">
                {{ $serviceRequestCount ?? 0 }}
            </p>
            <p class="text-[11px] text-gray-500 mt-1">
                Hari ini:
                <span class="font-semibold text-blue-500">
                    {{ $serviceRequestsToday ?? 0 }}
                </span>
            </p>

            @if(\Illuminate\Support\Facades\Route::has('admin.service-requests.index'))
                <a href="{{ route('admin.service-requests.index') }}"
                   class="inline-block mt-2 text-xs text-[var(--homi-blue)] hover:underline font-medium">
                    Ke pengajuan →
                </a>
            @endif
        </div>

    </div>

    {{-- ====== HIGHLIGHT PENGUMUMAN (1 utama + lainnya) ====== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Kolom kiri: pengumuman utama --}}
        <div class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">
                Pengumuman Utama
            </h2>

            @if(!empty($mainAnnouncement))
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    @if(!empty($mainAnnouncement->image_path))
                        <img src="{{ asset('storage/' . $mainAnnouncement->image_path) }}"
                             class="w-full h-56 object-cover" alt="Pengumuman">
                    @endif

                    <div class="p-4">
                        <p class="text-xs text-gray-500 mb-1">
                            {{ $mainAnnouncement->created_at?->format('d M Y') }}
                        </p>

                        @php
                            $canShowAnnouncement = \Illuminate\Support\Facades\Route::has('admin.announcements.show');
                        @endphp

                        @if($canShowAnnouncement)
                            <a href="{{ route('admin.announcements.show', $mainAnnouncement->id) }}"
                               class="text-xl font-semibold text-gray-900 mb-2 block hover:underline">
                                {{ $mainAnnouncement->title }}
                            </a>
                        @else
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                {{ $mainAnnouncement->title }}
                            </h3>
                        @endif

                        <p class="text-sm text-gray-600">
                            {{ \Illuminate\Support\Str::limit(strip_tags($mainAnnouncement->body), 140) }}
                        </p>

                        {{-- Link lihat semua pengumuman --}}
                        @if(\Illuminate\Support\Facades\Route::has('admin.announcements.index'))
                            <div class="mt-3">
                                <a href="{{ route('admin.announcements.index') }}"
                                   class="text-sm text-[var(--homi-blue)] hover:underline font-medium">
                                    Lihat semua pengumuman →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500">Belum ada pengumuman.</p>
            @endif
        </div>

        {{-- Kolom kanan: pengumuman lainnya --}}
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Pengumuman Lainnya</h2>

            @forelse(($nextAnnouncements ?? []) as $item)
                @php
                    $canShowAnnouncement = \Illuminate\Support\Facades\Route::has('admin.announcements.show');
                @endphp

                <div class="bg-white rounded-xl shadow p-3 mb-3 flex gap-3">
                    @if(!empty($item->image_path))
                        <img src="{{ asset('storage/' . $item->image_path) }}"
                             class="w-16 h-16 object-cover rounded-md" alt="Pengumuman">
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] text-gray-500">
                            {{ $item->created_at?->format('d M Y') }}
                        </p>

                        @if($canShowAnnouncement)
                            <a href="{{ route('admin.announcements.show', $item->id) }}"
                               class="text-sm font-semibold text-gray-900 hover:underline block truncate">
                                {{ $item->title }}
                            </a>
                        @else
                            <h3 class="text-sm font-semibold text-gray-900 truncate">
                                {{ $item->title }}
                            </h3>
                        @endif

                        <p class="text-xs text-gray-600 mt-1">
                            {{ \Illuminate\Support\Str::limit(strip_tags($item->body), 70) }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada pengumuman lain.</p>
            @endforelse
        </div>

    </div>

    {{-- ====== PEMBAYARAN TERBARU + RESIKO TUNGGAKAN ====== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Pembayaran --}}
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Pembayaran Terbaru</h2>

                @if(\Illuminate\Support\Facades\Route::has('admin.payments.index'))
                    <a href="{{ route('admin.payments.index') }}"
                       class="text-xs text-[var(--homi-blue)] hover:underline font-medium mt-1">
                        Lihat semua →
                    </a>
                @endif
            </div>

            @forelse(($latestPayments ?? []) as $pay)
                <div class="flex justify-between items-center py-2 border-b last:border-b-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            Rp {{ number_format($pay->amount ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $pay->user->full_name ?? $pay->user->username ?? 'Warga' }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-[11px] text-gray-500">
                            {{ $pay->created_at?->format('d M Y') }}
                        </p>

                        @php($st = strtolower($pay->status ?? 'unknown'))
                        <span class="inline-flex px-2 py-1 rounded-full text-[11px]
                            @if($st === 'paid')
                                bg-green-100 text-green-700
                            @elseif($st === 'pending')
                                bg-yellow-100 text-yellow-700
                            @elseif($st === 'failed')
                                bg-red-100 text-red-700
                            @elseif($st === 'canceled' || $st === 'cancelled')
                                bg-gray-100 text-gray-700
                            @else
                                bg-gray-100 text-gray-700
                            @endif">
                            {{ strtoupper($st) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada data pembayaran.</p>
            @endforelse
        </div>

        {{-- Resiko Tunggakan --}}
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Resiko Tunggakan</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Ringkasan warga yang berpotensi menunggak iuran (prioritas follow-up).
                    </p>
                </div>

                @if(\Illuminate\Support\Facades\Route::has('admin.fees.invoices.index'))
                    <a href="{{ route('admin.fees.invoices.index') }}"
                       class="text-xs text-[var(--homi-blue)] hover:underline font-medium mt-1">
                        Lihat tagihan →
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="rounded-xl border border-gray-200 p-3">
                    <div class="text-[11px] text-gray-500">Warga Berisiko</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $arrearsSummary['at_risk_count'] ?? 0 }}
                    </div>
                    <div class="text-[11px] text-gray-500 mt-1">
                        Menunggak ≥ {{ $arrearsSummary['at_risk_days'] ?? 14 }} hari
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 p-3">
                    <div class="text-[11px] text-gray-500">Resiko Tinggi</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $arrearsSummary['high_risk_count'] ?? 0 }}
                    </div>
                    <div class="text-[11px] text-gray-500 mt-1">
                        Menunggak ≥ {{ $arrearsSummary['high_risk_days'] ?? 30 }} hari
                    </div>
                </div>

                <div class="col-span-2 rounded-xl border border-gray-200 p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-[11px] text-gray-500">Estimasi Total Tunggakan</div>
                            <div class="text-xl font-semibold text-gray-900">
                                Rp {{ number_format($arrearsSummary['total_arrears_amount'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Optional CTA kalau kamu kasih dari controller --}}
                        @if(!empty($arrearsSummary['cta_url']) && !empty($arrearsSummary['cta_label']))
                            <a href="{{ $arrearsSummary['cta_url'] }}"
                               class="px-3 py-2 rounded-lg bg-[var(--homi-orange)] text-white text-xs font-semibold hover:bg-orange-500">
                                {{ $arrearsSummary['cta_label'] }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="text-sm font-semibold text-gray-800 mb-2">Top Menunggak</div>

                @php($top = $arrearsSummary['top_arrears'] ?? [])
                @if(count($top))
                    <div class="space-y-2">
                        @foreach($top as $row)
                            <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $row['name'] ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-gray-500">
                                        Blok {{ $row['blok'] ?? '-' }} / No {{ $row['no_rumah'] ?? '-' }}
                                        • {{ $row['days_overdue'] ?? 0 }} hari
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($row['amount'] ?? 0, 0, ',', '.') }}
                                    </div>
                                    @if(!empty($row['action_url']))
                                        <a href="{{ $row['action_url'] }}"
                                           class="text-[11px] text-[var(--homi-blue)] hover:underline font-medium">
                                            Detail
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        Belum ada data tunggakan yang terdeteksi.
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ====== GRAFIK KEUANGAN ====== --}}
    <div class="bg-white rounded-xl shadow p-4 mt-2">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Grafik Pembayaran Warga</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Perbandingan total lunas vs pending per bulan dan komposisi status pembayaran.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
            {{-- Combo: Bar + Line --}}
            <div class="lg:col-span-2">
                <div class="h-72">
                    <canvas id="chart-payments-monthly" class="w-full h-full"></canvas>
                </div>
            </div>

            {{-- Donut --}}
            <div>
                <div class="h-72">
                    <canvas id="chart-payments-status" class="w-full h-full"></canvas>
                </div>

                <div class="mt-3 text-xs text-gray-600 space-y-1">
                    <p><span class="inline-block w-3 h-3 bg-green-400 rounded"></span> Sudah Dibayar</p>
                    <p><span class="inline-block w-3 h-3 bg-yellow-400 rounded"></span> Pending</p>
                    <p><span class="inline-block w-3 h-3 bg-red-400 rounded"></span> Gagal</p>
                    <p><span class="inline-block w-3 h-3 bg-gray-400 rounded"></span> Dibatalkan</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rupiah = (n) => new Intl.NumberFormat('id-ID').format(Number(n || 0));

    // ==== DATA ====
    const monthlyLabels = @json($chartMonthly['labels'] ?? []);
    const paidMonthly   = @json($chartMonthly['paid'] ?? ($chartMonthly['data'] ?? []));
    const pendingMonthly= @json($chartMonthly['pending'] ?? []);

    const statusLabels  = @json($chartStatus['labels'] ?? []);
    const statusData    = @json($chartStatus['data'] ?? []);

    // ==== GLOBAL OPTIONS ====
    Chart.defaults.font.family = "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial";
    Chart.defaults.plugins.legend.labels.boxWidth = 12;

    // ==== COMBO CHART: Bar (Lunas) + Line (Pending) ====
    const elBar = document.getElementById('chart-payments-monthly');
    if (elBar) {
        new Chart(elBar, {
            data: {
                labels: monthlyLabels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Lunas (Rp)',
                        data: paidMonthly,
                        backgroundColor: 'rgba(47, 121, 160, 0.35)',
                        borderColor: 'rgba(47, 121, 160, 1)',
                        borderWidth: 1,
                        borderRadius: 10,
                        maxBarThickness: 38
                    },
                    {
                        type: 'line',
                        label: 'Pending (Rp)',
                        data: pendingMonthly.length ? pendingMonthly : (paidMonthly || []).map(() => 0),
                        borderColor: 'rgba(248, 164, 119, 1)',
                        backgroundColor: 'rgba(248, 164, 119, 0.15)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: Rp ${rupiah(ctx.raw)}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxRotation: 0 }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Rp ' + rupiah(value)
                        }
                    }
                }
            }
        });
    }

    // ==== DONUT ====
    const elDonut = document.getElementById('chart-payments-status');
    if (elDonut) {
        new Chart(elDonut, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: [
                        'rgba(34,197,94,0.35)',   // paid
                        'rgba(234,179,8,0.35)',   // pending
                        'rgba(239,68,68,0.35)',   // failed
                        'rgba(107,114,128,0.35)'  // canceled
                    ],
                    borderColor: [
                        'rgba(34,197,94,1)',
                        'rgba(234,179,8,1)',
                        'rgba(239,68,68,1)',
                        'rgba(107,114,128,1)'
                    ],
                    borderWidth: 1,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.label}: ${rupiah(ctx.raw)}`
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
