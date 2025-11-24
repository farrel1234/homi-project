@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-1">
            <h1 class="homi-title">
                Dashboard HOMI Admin
            </h1>
            <p class="homi-subtitle">
                Pantau data warga, pengumuman, dan pembayaran iuran perumahan Hawai Garden.
            </p>
        </div>

        {{-- KARTU RINGKASAN STATISTIK --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Total Warga --}}
            <div class="homi-card border-l-4 border-[var(--homi-blue)] flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">
                        Total Warga Terdaftar
                    </p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">
                        @isset($totalResidents)
                            {{ number_format($totalResidents, 0, ',', '.') }}
                        @else
                            -
                        @endisset
                    </p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        Data warga yang sudah terinput di sistem.
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[var(--homi-blue)]/10 flex items-center justify-center">
                    <span class="text-[var(--homi-blue)] text-lg font-semibold">👥</span>
                </div>
            </div>

            {{-- Pengumuman --}}
            <div class="homi-card border-l-4 border-[var(--homi-orange)] flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">
                        Total Pengumuman
                    </p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">
                        @isset($totalAnnouncements)
                            {{ number_format($totalAnnouncements, 0, ',', '.') }}
                        @else
                            -
                        @endisset
                    </p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        Informasi yang sudah dibagikan kepada warga.
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[var(--homi-orange)]/10 flex items-center justify-center">
                    <span class="text-[var(--homi-orange)] text-lg font-semibold">📢</span>
                </div>
            </div>

            {{-- Pembayaran Pending --}}
            <div class="homi-card border-l-4 border-amber-400 flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">
                        Pembayaran Menunggu Diproses
                    </p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">
                        @isset($pendingPaymentsCount)
                            {{ number_format($pendingPaymentsCount, 0, ',', '.') }}
                        @else
                            -
                        @endisset
                    </p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        Pembayaran iuran dengan status <span class="font-semibold">pending</span>.
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-400/10 flex items-center justify-center">
                    <span class="text-amber-500 text-lg font-semibold">💳</span>
                </div>
            </div>
        </div>

        {{-- DUA PANEL: PENGUMUMAN & PEMBAYARAN --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- PANEL PENGUMUMAN TERBARU --}}
            <div class="homi-card space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">
                            Pengumuman Terbaru
                        </h2>
                        <p class="text-[12px] text-gray-500">
                            Ringkasan beberapa pengumuman terakhir.
                        </p>
                    </div>
                    <a href="{{ route('announcements.index') }}"
                       class="text-[12px] font-medium text-[var(--homi-blue)] hover:underline">
                        Lihat semua
                    </a>
                </div>

                @isset($latestAnnouncements)
                    @if($latestAnnouncements->count())
                        <div class="divide-y divide-gray-100">
                            @foreach($latestAnnouncements as $ann)
                                <div class="py-3 flex flex-col gap-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <h3 class="text-sm font-semibold text-gray-800 line-clamp-1">
                                            {{ $ann->title }}
                                        </h3>
                                        <span class="text-[11px] text-gray-400 whitespace-nowrap">
                                            {{ optional($ann->created_at)->format('d M Y') }}
                                        </span>
                                    </div>
                                    <p class="text-[12px] text-gray-600 line-clamp-2">
                                        {{ $ann->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($ann->content ?? ''), 90) }}
                                    </p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-50 text-[10px] text-orange-700 border border-orange-100">
                                            {{ $ann->category ?? 'Umum' }}
                                        </span>
                                        <a href="{{ route('announcements.edit', $ann->id) }}"
                                           class="text-[11px] text-[var(--homi-blue)] hover:underline">
                                            Kelola
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-[12px] text-gray-400">
                            Belum ada pengumuman yang dibuat.
                        </p>
                    @endif
                @else
                    <p class="text-[12px] text-gray-400">
                        Data pengumuman belum dikirim dari controller.
                    </p>
                @endisset
            </div>

            {{-- PANEL PEMBAYARAN TERBARU --}}
            <div class="homi-card space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">
                            Pembayaran Terbaru
                        </h2>
                        <p class="text-[12px] text-gray-500">
                            Beberapa transaksi pembayaran terakhir dari warga.
                        </p>
                    </div>
                    <a href="{{ route('payments.index') }}"
                       class="text-[12px] font-medium text-[var(--homi-blue)] hover:underline">
                        Lihat semua
                    </a>
                </div>

                @isset($recentPayments)
                    @if($recentPayments->count())
                        <div class="overflow-x-auto">
                            <table class="homi-table">
                                <thead>
                                <tr>
                                    <th class="text-left">Warga</th>
                                    <th class="text-left">Keterangan</th>
                                    <th class="text-right">Jumlah</th>
                                    <th class="text-center">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recentPayments as $payment)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-[13px] text-gray-900">
                                                {{ $payment->user->full_name ?? $payment->user->username ?? '-' }}
                                            </div>
                                            <div class="text-[11px] text-gray-400">
                                                {{ $payment->user->email ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="text-[12px] align-top">
                                            {{ $payment->description ?? '-' }}
                                        </td>
                                        <td class="text-right text-[13px] font-semibold align-top">
                                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center align-top">
                                            @php
                                                $label = [
                                                    'pending'   => 'Belum Diproses',
                                                    'paid'      => 'Sudah Dibayar',
                                                    'failed'    => 'Ditolak / Gagal',
                                                    'cancelled' => 'Dibatalkan',
                                                ][$payment->status] ?? $payment->status;

                                                $badgeClass = match ($payment->status) {
                                                    'pending'   => 'bg-amber-100 text-amber-800 border border-amber-200',
                                                    'paid'      => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                                    'failed'    => 'bg-rose-100 text-rose-800 border border-rose-200',
                                                    'cancelled' => 'bg-gray-100 text-gray-700 border border-gray-200',
                                                    default     => 'bg-gray-100 text-gray-700 border border-gray-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium {{ $badgeClass }}">
                                                {{ $label }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-[12px] text-gray-400">
                            Belum ada transaksi pembayaran yang tercatat.
                        </p>
                    @endif
                @else
                    <p class="text-[12px] text-gray-400">
                        Data pembayaran belum dikirim dari controller.
                    </p>
                @endisset
            </div>
        </div>
    </div>
@endsection
