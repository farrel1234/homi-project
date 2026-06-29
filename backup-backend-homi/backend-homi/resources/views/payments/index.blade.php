@extends('layouts.app')

@section('title', 'Pembayaran Iuran')

@section('content')
@php
    /**
     * Period helpers:
     * - key: "YYYY-MM" untuk grouping/sort
     * - label: "Juni 2026" untuk tampilan
     */
    function period_key($p) {
        if (!$p) return 'unknown';

        if ($p instanceof \Illuminate\Support\Carbon) {
            return $p->format('Y-m');
        }

        $s = trim((string) $p);

        // "YYYY-MM"
        if (preg_match('/^\d{4}-\d{2}$/', $s)) return $s;

        // "YYYY-MM-DD" => ambil YYYY-MM
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return substr($s, 0, 7);

        // fallback parse
        try {
            return \Carbon\Carbon::parse($s)->format('Y-m');
        } catch (\Throwable $e) {
            return 'unknown';
        }
    }

    function period_label_from_key($key) {
        if (!$key || $key === 'unknown') return 'Tanpa Periode';
        try {
            return \Carbon\Carbon::createFromFormat('Y-m', $key)->translatedFormat('F Y');
        } catch (\Throwable $e) {
            return $key;
        }
    }

    function money_idr($n) {
        if ($n === null || $n === '') return '-';
        return 'Rp ' . number_format((float)$n, 0, ',', '.');
    }

    // kalau paginator: ambil koleksi untuk grouping
    $rows = method_exists($payments, 'getCollection') ? $payments->getCollection() : collect($payments);

    // group by period (YYYY-MM)
    $groups = $rows->groupBy(function ($payment) {
        return period_key(optional($payment->invoice)->period);
    });

    // sort group key desc (2026-12, 2026-11, ...)
    $groups = $groups->sortKeysDesc();
@endphp

<div class="space-y-6">

    {{-- Judul Halaman --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="homi-title">Monitoring Iuran Warga</h1>
            <p class="homi-subtitle">Kelola dan review konfirmasi pembayaran dari seluruh warga</p>
        </div>
        <div class="flex items-center gap-2 bg-slate-100 px-4 py-2 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Total Data:</span>
            <span class="text-sm font-black text-[var(--homi-blue)]">{{ $payments->total() }}</span>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-100 flex items-center gap-3">
             <svg viewBox="0 0 24 24" class="h-5 w-5 text-emerald-500 fill-none stroke-current stroke-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Panel --}}
    <div class="homi-card">
        <form method="GET" action="{{ route('payments.index') }}" class="flex flex-col lg:flex-row lg:items-end gap-4">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="homi-label">Status Pembayaran</label>
                    <select name="status" class="homi-input">
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>Menunggu Review</option>
                        <option value="paid" @selected(request('status') === 'paid')>Telah Disetujui</option>
                        <option value="failed" @selected(request('status') === 'failed')>Ditolak</option>
                    </select>
                </div>
                <div class="md:col-span-1 lg:col-span-2 space-y-1">
                    <label class="homi-label">Cari Warga / Keterangan</label>
                    <div class="relative group">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Masukkan nama warga atau ID transaksi..." class="homi-input pr-10">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[var(--homi-blue)] transition-colors">
                            <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="homi-btn homi-btn-primary px-8">Filter</button>
                @if(request('q') || request('status'))
                    <a href="{{ route('payments.index') }}" class="homi-btn homi-btn-secondary px-4 text-rose-600 border-rose-100 bg-rose-50/50">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bulk action --}}
    <form method="POST" action="{{ route('payments.bulk') }}" class="space-y-6">
        @csrf

        <div class="bg-slate-900 rounded-3xl p-5 shadow-xl border border-slate-800 flex flex-col lg:flex-row lg:items-center justify-between gap-5 sticky top-4 z-20">
            <div class="flex items-center gap-4">
                <div class="checkbox-wrapper bg-white/10 p-2 rounded-xl border border-white/10">
                    <input type="checkbox" class="select-all h-5 w-5 rounded border-white/20 bg-white/5 text-[var(--homi-blue)] focus:ring-offset-slate-900">
                </div>
                <div>
                    <div class="text-white font-bold text-sm tracking-wide">Aksi Massal</div>
                    <div class="text-slate-400 text-[10px] uppercase font-bold tracking-widest">Pilih data untuk diproses sekaligus</div>
                </div>
            </div>

            <div class="flex-1 max-w-2xl flex flex-col sm:flex-row gap-3">
                <input type="text" name="reason" placeholder="Berikan catatan singkat (opsional)..." 
                       class="w-full bg-white/5 border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-[var(--homi-blue)] focus:border-transparent">
                
                <div class="flex gap-2">
                    <button type="submit" name="action" value="approve" 
                            class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl bg-emerald-500 text-white text-xs font-black uppercase tracking-wider hover:bg-emerald-600 transition shadow-[0_4px_12px_rgba(16,185,129,0.3)]">
                        Terima
                    </button>
                    <button type="submit" name="action" value="reject" 
                            class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl bg-rose-500 text-white text-xs font-black uppercase tracking-wider hover:bg-rose-600 transition shadow-[0_4px_12px_rgba(244,63,94,0.3)]">
                        Tolak
                    </button>
                </div>
            </div>
        </div>

        {{-- LIST PER BULAN --}}
        <div class="space-y-8">
            @forelse($groups as $pKey => $list)
                @php
                    $labelBulan = period_label_from_key($pKey);
                    $countBulan = $list->count();
                @endphp

                <div class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-2xl bg-[var(--homi-blue)] flex items-center justify-center text-white shadow-lg shadow-sky-200">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-800 text-lg leading-none">{{ $labelBulan }}</h3>
                                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-1">{{ $countBulan }} Data Pembayaran</p>
                            </div>
                        </div>
                        
                        <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 hover:bg-slate-200 transition-colors cursor-pointer group">
                            <input type="checkbox" class="select-month h-4 w-4 rounded border-slate-300 text-[var(--homi-blue)]" data-month="{{ $pKey }}">
                            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest group-hover:text-slate-900">Pilih Semua</span>
                        </label>
                    </div>

                    <div class="homi-card p-0 overflow-hidden border-slate-200">
                        {{-- ===== DESKTOP TABLE ===== --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="homi-table w-full">
                                <thead>
                                    <tr>
                                        <th class="w-12 text-center">Sel</th>
                                        <th>Warga</th>
                                        <th>Keterangan Iuran</th>
                                        <th class="text-right">Nominal</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($list as $payment)
                                        @php
                                            $payer   = $payment->payer ?? $payment->user;
                                            $name    = $payer->full_name ?? $payer->name ?? $payer->username ?? '-';
                                            $email   = $payer->email ?? null;
                                            $invoice = $payment->invoice;
                                            $feeName = $invoice?->feeType?->name ?? '-';
                                            $trxId   = $invoice?->trx_id ?? '-';
                                            $amount  = $invoice?->amount;
                                            $rs = $payment->review_status;
                                            $monthKeyRow = period_key(optional($invoice)->period);
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="text-center">
                                                <input type="checkbox" name="selected[]" value="{{ $payment->id }}" 
                                                       class="row-checkbox h-4 w-4 rounded border-slate-300 text-[var(--homi-blue)]" 
                                                       data-month="{{ $monthKeyRow }}">
                                            </td>
                                            <td>
                                                <div class="font-bold text-slate-800">{{ $name }}</div>
                                                <div class="text-[10px] text-slate-500 font-mono">{{ $email ?? $payer->phone ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <div class="text-sm font-semibold text-slate-700">{{ $feeName }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">TRX: {{ $trxId }}</div>
                                            </td>
                                            <td class="text-right font-black text-slate-900">
                                                {{ money_idr($amount) }}
                                            </td>
                                            <td class="text-center">
                                                <span class="homi-badge scale-90 {{ 
                                                    match($rs) {
                                                        'pending' => 'homi-badge-pending',
                                                        'approved' => 'homi-badge-success',
                                                        'rejected' => 'homi-badge-danger',
                                                        default => 'homi-badge-info'
                                                    }
                                                }}">
                                                    {{ match($rs) { 'pending' => 'Pending', 'approved' => 'Selesai', 'rejected' => 'Ditolak', default => $rs } }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('payments.show', $payment->id) }}" 
                                                   class="homi-btn homi-btn-secondary py-1 text-[11px]">Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ===== MOBILE CARD LIST ===== --}}
                        <div class="md:hidden divide-y divide-slate-100">
                            @foreach($list as $payment)
                                @php
                                    $payer   = $payment->payer ?? $payment->user;
                                    $name    = $payer->full_name ?? $payer->name ?? $payer->username ?? '-';
                                    $invoice = $payment->invoice;
                                    $feeName = $invoice?->feeType?->name ?? '-';
                                    $amount  = $invoice?->amount;
                                    $rs = $payment->review_status;
                                    $monthKeyRow = period_key(optional($invoice)->period);
                                @endphp
                                <div class="p-4 flex flex-col gap-3 relative">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="selected[]" value="{{ $payment->id }}" 
                                                   class="row-checkbox h-5 w-5 rounded border-slate-300 text-[var(--homi-blue)]" 
                                                   data-month="{{ $monthKeyRow }}">
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-800 truncate">{{ $name }}</div>
                                                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ $feeName }}</div>
                                            </div>
                                        </div>
                                        <span class="homi-badge scale-75 origin-right {{ 
                                                    match($rs) {
                                                        'pending' => 'homi-badge-pending',
                                                        'approved' => 'homi-badge-success',
                                                        'rejected' => 'homi-badge-danger',
                                                        default => 'homi-badge-info'
                                                    }
                                                }}">
                                            {{ match($rs) { 'pending' => 'Pending', 'approved' => 'Paid', 'rejected' => 'Fail', default => $rs } }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-lg font-black text-[var(--homi-blue)]">{{ money_idr($amount) }}</div>
                                        <a href="{{ route('payments.show', $payment->id) }}" class="text-xs font-bold text-sky-600 underline">Lihat Detail &rarr;</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                    <svg viewBox="0 0 24 24" class="h-12 w-12 mx-auto text-slate-300 opacity-50 mb-3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Belum ada riwayat pembayaran</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $payments->links() }}
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.querySelector('.select-all');

    function getRowChecks(){
        return document.querySelectorAll('.row-checkbox');
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            getRowChecks().forEach(cb => cb.checked = selectAll.checked);
            document.querySelectorAll('.select-month').forEach(m => m.checked = selectAll.checked);
        });
    }

    // Select all per bulan
    document.querySelectorAll('.select-month').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const key = toggle.getAttribute('data-month');
            document.querySelectorAll('.row-checkbox[data-month="'+ key +'"]').forEach(cb => {
                cb.checked = toggle.checked;
            });
        });
    });
});
</script>
@endpush
@endsection
