@extends('layouts.app')

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
        </div>

        {{-- Pembayaran Pending --}}
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-500 mb-1">Pembayaran Menunggu Diproses</p>
            <p class="text-3xl font-semibold text-gray-900">
                {{ $pendingPaymentsCount ?? 0 }}
            </p>
            <p class="text-[11px] text-gray-500 mt-1">
                Total tagihan pending: 
                <span class="font-semibold text-orange-500">
                    Rp {{ number_format($totalPendingAmount ?? 0, 0, ',', '.') }}
                </span>
            </p>
        </div>

        {{-- Pengajuan Layanan --}}
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-500 mb-1">Pengajuan Layanan</p>
            <p class="text-3xl font-semibold text-gray-900">
                {{ $serviceRequestCount ?? 0 }}
            </p>
            <p class="text-[11px] text-gray-500 mt-1">
                Hari ini: <span class="font-semibold text-blue-500">
                    {{ $serviceRequestsToday ?? 0 }}
                </span>
            </p>
        </div>

    </div>

    {{-- ====== HIGHLIGHT PENGUMUMAN (1 utama + 3 lainnya) ====== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Kolom kiri: pengumuman utama --}}
        <div class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">
                Pengumuman Utama
            </h2>

            @if($mainAnnouncement)
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    @if($mainAnnouncement->image_path)
                        <img src="{{ asset('storage/' . $mainAnnouncement->image_path) }}"
                             class="w-full h-56 object-cover">
                    @endif

                    <div class="p-4">
                        <p class="text-xs text-gray-500 mb-1">
                            {{ $mainAnnouncement->created_at?->format('d M Y') }}
                        </p>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ $mainAnnouncement->title }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ \Illuminate\Support\Str::limit(strip_tags($mainAnnouncement->body), 140) }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500">Belum ada pengumuman.</p>
            @endif
        </div>

        {{-- Kolom kanan: pengumuman lainnya --}}
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Pengumuman Lainnya</h2>

            @forelse($nextAnnouncements as $item)
                <div class="bg-white rounded-xl shadow p-3 mb-3 flex gap-3">
                    @if($item->image_path)
                        <img src="{{ asset('storage/' . $item->image_path) }}"
                             class="w-16 h-16 object-cover rounded-md">
                    @endif

                    <div class="flex-1">
                        <p class="text-[11px] text-gray-500">
                            {{ $item->created_at?->format('d M Y') }}
                        </p>
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ $item->title }}
                        </h3>
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

    {{-- ====== PEMBAYARAN TERBARU ====== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Pembayaran --}}
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Pembayaran Terbaru</h2>

            @forelse($latestPayments as $pay)
                <div class="flex justify-between items-center py-2 border-b last:border-b-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            Rp {{ number_format($pay->amount, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $pay->user->full_name ?? $pay->user->username ?? 'Warga' }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-[11px] text-gray-500">
                            {{ $pay->created_at?->format('d M Y') }}
                        </p>

                        <span class="inline-flex px-2 py-1 rounded-full text-[11px]
                            @if($pay->status === 'paid')
                                bg-green-100 text-green-700
                            @elseif($pay->status === 'pending')
                                bg-yellow-100 text-yellow-700
                            @elseif($pay->status === 'failed')
                                bg-red-100 text-red-700
                            @else
                                bg-gray-100 text-gray-700
                            @endif">
                            {{ strtoupper($pay->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada data pembayaran.</p>
            @endforelse
        </div>

        {{-- Tempat tambahan --}}
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Ringkasan Lain</h2>
            <p class="text-sm text-gray-500">
                Ruang ini bisa kamu pakai nanti untuk grafik, log aktivitas, dsb.
            </p>
        </div>

    </div>

    {{-- ====== GRAFIK KEUANGAN BARU ====== --}}
    <div class="bg-white rounded-xl shadow p-4 mt-8">

        <h2 class="text-lg font-semibold text-gray-800 mb-3">Grafik Pembayaran Warga</h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Bar Chart: total lunas --}}
            <div class="lg:col-span-2">
                <canvas id="chart-payments-monthly" class="w-full h-64"></canvas>
            </div>

            {{-- Doughnut: komposisi status --}}
            <div>
                <canvas id="chart-payments-status" class="w-full h-64"></canvas>

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

    const monthlyLabels = @json($chartMonthly['labels']);
    const monthlyData   = @json($chartMonthly['data']);

    const statusLabels  = @json($chartStatus['labels']);
    const statusData    = @json($chartStatus['data']);

    // BAR CHART
    new Chart(document.getElementById('chart-payments-monthly'), {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Total Lunas (Rp)',
                data: monthlyData,
                borderWidth: 1
            }]
        },
        options: { responsive: true }
    });

    // DOUGHNUT CHART
    new Chart(document.getElementById('chart-payments-status'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                borderWidth: 1
            }]
        },
        options: { responsive: true }
    });

});
</script>
@endpush
