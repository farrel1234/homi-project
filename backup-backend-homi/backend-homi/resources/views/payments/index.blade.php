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

<div class="space-y-5">

    {{-- Judul Halaman --}}
    <div>
        <div class="homi-title">Pembayaran Iuran Warga</div>
        <div class="homi-subtitle">
            Data pembayaran dipisah per bulan berdasarkan periode tagihan.
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="p-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-3 rounded-lg bg-rose-50 text-rose-700 text-sm border border-rose-100">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-3 rounded-lg bg-rose-50 text-rose-700 text-sm border border-rose-100">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Panel Utama --}}
    <div class="homi-card space-y-4">

        {{-- Filter --}}
        <form method="GET" action="{{ route('payments.index') }}"
              class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">

            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2 w-full lg:w-auto">
                <div class="text-sm font-semibold text-gray-700">Filter:</div>

                <select name="status"
                        class="w-full sm:w-52 border border-[var(--homi-border)] rounded-full px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <option value="">Semua status</option>
                    <option value="pending"  @selected((request('status')) === 'pending')>Belum Diproses</option>
                    <option value="paid"     @selected((request('status')) === 'paid')>Disetujui</option>
                    <option value="failed"   @selected((request('status')) === 'failed')>Ditolak</option>
                </select>

                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Cari nama warga / keterangan"
                       class="w-full sm:w-72 border border-[var(--homi-border)] rounded-full px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-sky-200">

                <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 rounded-full bg-[var(--homi-blue)] text-white text-sm font-semibold hover:opacity-95">
                    Tampilkan
                </button>

                @if(request('q') || request('status'))
                    <a href="{{ route('payments.index') }}" class="text-xs text-gray-500 hover:underline text-center sm:text-left">
                        Reset
                    </a>
                @endif
            </div>

            <div class="text-xs text-gray-500">
                Total data:
                <span class="font-semibold text-gray-700">{{ $payments->total() }}</span>
            </div>
        </form>

        {{-- Bulk action --}}
        <form method="POST" action="{{ route('payments.bulk') }}" class="space-y-3">
            @csrf

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="text-sm font-semibold text-gray-700">
                    Aksi untuk data terpilih:
                </div>

                <div class="flex-1 flex flex-col sm:flex-row gap-2 sm:items-center">
                    <input type="text"
                           name="reason"
                           placeholder="Catatan admin (opsional)..."
                           class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-sky-200">

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="submit" name="action" value="approve"
                                class="w-full sm:w-auto px-4 py-2 rounded-lg bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">
                            Setujui
                        </button>

                        <button type="submit" name="action" value="reject"
                                class="w-full sm:w-auto px-4 py-2 rounded-lg bg-rose-500 text-white text-sm font-semibold hover:bg-rose-600">
                            Tolak
                        </button>
                    </div>
                </div>
            </div>

            {{-- Global select all --}}
            <div class="flex items-center gap-2 text-xs text-gray-600">
                <input type="checkbox" class="select-all rounded border-gray-300">
                <span>Pilih semua pada halaman ini</span>
            </div>

            {{-- LIST PER BULAN --}}
            <div class="space-y-4">
                @forelse($groups as $pKey => $list)
                    @php
                        $labelBulan = period_label_from_key($pKey);
                        $countBulan = $list->count();
                    @endphp

                    <div class="border border-[var(--homi-border)] rounded-xl overflow-hidden bg-white">
                        {{-- Header bulan --}}
                        <div class="px-4 py-3 bg-orange-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="text-sm font-semibold text-gray-800">
                                {{ $labelBulan }}
                                <span class="ml-2 text-xs font-normal text-gray-500">({{ $countBulan }} data)</span>
                            </div>

                            {{-- Select all per bulan --}}
                            <label class="inline-flex items-center gap-2 text-xs text-gray-600">
                                <input type="checkbox" class="select-month rounded border-gray-300" data-month="{{ $pKey }}">
                                Pilih bulan ini
                            </label>
                        </div>

                        {{-- ===== MOBILE: CARD LIST ===== --}}
                        <div class="p-4 space-y-3 md:hidden">
                            @foreach($list as $payment)
                                @php
                                    $payer   = $payment->payer ?? $payment->user;
                                    $name    = $payer->full_name ?? $payer->name ?? $payer->username ?? '-';
                                    $email   = $payer->email ?? null;

                                    $invoice = $payment->invoice;
                                    $feeName = $invoice?->feeType?->name ?? '-';
                                    $trxId   = $invoice?->trx_id ?? '-';

                                    $amount  = $invoice?->amount;
                                    $dueDate = $invoice?->due_date;

                                    $rs = $payment->review_status; // pending/approved/rejected
                                    $label = [
                                        'pending'  => 'Belum Diproses',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                    ][$rs] ?? ($rs ?? '-');

                                    $badgeClass = match ($rs) {
                                        'pending'  => 'bg-amber-100 text-amber-800',
                                        'approved' => 'bg-emerald-100 text-emerald-800',
                                        'rejected' => 'bg-rose-100 text-rose-800',
                                        default    => 'bg-gray-100 text-gray-700',
                                    };

                                    $paidAt = $payment->created_at;

                                    $monthKeyRow = period_key(optional($invoice)->period);
                                @endphp

                                <div class="rounded-xl border border-[var(--homi-border)] bg-white p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-900 break-words">{{ $name }}</div>
                                            @if($email)
                                                <div class="text-[11px] text-gray-500 break-words">{{ $email }}</div>
                                            @endif
                                        </div>

                                        <div class="shrink-0 flex items-center gap-2">
                                            <input type="checkbox"
                                                   name="selected[]"
                                                   value="{{ $payment->id }}"
                                                   class="row-checkbox rounded border-gray-300"
                                                   data-month="{{ $monthKeyRow }}">
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-1 gap-2 text-sm">
                                        <div>
                                            <div class="text-[11px] text-gray-500">Iuran</div>
                                            <div class="font-semibold text-gray-900">{{ $feeName }}</div>
                                            <div class="text-[11px] text-gray-500 mt-1">TRX: <span class="font-mono">{{ $trxId }}</span></div>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-[11px] text-gray-500">Jumlah</div>
                                                <div class="font-semibold text-gray-900">{{ money_idr($amount) }}</div>
                                            </div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                                                {{ $label }}
                                            </span>
                                        </div>

                                        <div class="flex items-center justify-between text-[12px] text-gray-600">
                                            <span>Jatuh tempo: {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d M Y') : '-' }}</span>
                                            <span>Dibayar: {{ $paidAt ? $paidAt->format('d M Y H:i') : '-' }}</span>
                                        </div>

                                        <div class="text-[12px] text-gray-700 break-words">
                                            <span class="text-gray-500">Catatan:</span> {{ $payment->note ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <a href="{{ route('payments.show', $payment->id) }}"
                                           class="w-full inline-flex justify-center items-center px-3 py-2 rounded-lg text-xs font-semibold border border-sky-200 text-sky-700 hover:bg-sky-50">
                                            Lihat detail
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- ===== DESKTOP: TABLE ===== --}}
                        <div class="overflow-x-auto hidden md:block">
                            <table class="homi-table min-w-[1100px]" style="border:0; border-radius:0;">
                                <thead>
                                    <tr>
                                        <th class="text-left w-20">Pilih</th>
                                        <th class="text-left">Warga</th>
                                        <th class="text-left">Iuran</th>
                                        <th class="text-left">Jumlah</th>
                                        <th class="text-left">Jatuh Tempo</th>
                                        <th class="text-left">Status</th>
                                        <th class="text-left">Dibayar</th>
                                        <th class="text-left">Catatan</th>
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
                                            $dueDate = $invoice?->due_date;

                                            $rs = $payment->review_status; // pending/approved/rejected
                                            $label = [
                                                'pending'  => 'Belum Diproses',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                            ][$rs] ?? ($rs ?? '-');

                                            $badgeClass = match ($rs) {
                                                'pending'  => 'bg-amber-100 text-amber-800',
                                                'approved' => 'bg-emerald-100 text-emerald-800',
                                                'rejected' => 'bg-rose-100 text-rose-800',
                                                default    => 'bg-gray-100 text-gray-700',
                                            };

                                            $paidAt = $payment->created_at;

                                            $monthKeyRow = period_key(optional($invoice)->period);
                                        @endphp

                                        <tr>
                                            <td class="whitespace-nowrap">
                                                <input type="checkbox"
                                                       name="selected[]"
                                                       value="{{ $payment->id }}"
                                                       class="row-checkbox rounded border-gray-300"
                                                       data-month="{{ $monthKeyRow }}">
                                            </td>

                                            <td>
                                                <div class="font-semibold text-gray-900 text-sm">{{ $name }}</div>
                                                @if($email)
                                                    <div class="text-[11px] text-gray-500">{{ $email }}</div>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="font-semibold text-gray-900 text-sm">{{ $feeName }}</div>
                                                <div class="text-[11px] text-gray-500 mt-1">
                                                    TRX: <span class="font-mono">{{ $trxId }}</span>
                                                </div>
                                            </td>

                                            <td class="whitespace-nowrap font-semibold text-gray-900">
                                                {{ money_idr($amount) }}
                                            </td>

                                            <td class="whitespace-nowrap text-sm text-gray-700">
                                                {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d M Y') : '-' }}
                                            </td>

                                            <td>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                                                    {{ $label }}
                                                </span>
                                            </td>

                                            <td class="whitespace-nowrap text-sm text-gray-700">
                                                {{ $paidAt ? $paidAt->format('d M Y H:i') : '-' }}
                                            </td>

                                            <td class="text-sm text-gray-700">
                                                {{ $payment->note ?? '-' }}
                                            </td>

                                            <td class="text-right whitespace-nowrap">
                                                <a href="{{ route('payments.show', $payment->id) }}"
                                                   class="px-3 py-2 rounded-lg text-xs font-semibold border border-sky-200 text-sky-700 hover:bg-sky-50">
                                                    Lihat detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                @empty
                    <div class="py-8 text-center text-gray-500">
                        Belum ada data pembayaran.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="pt-1">
                {{ $payments->links() }}
            </div>
        </form>
    </div>
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
