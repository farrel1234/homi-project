@extends('layouts.app')

@section('title', 'Tagihan Iuran')

@section('content')
<div class="homi-card">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
        <div class="min-w-0">
            <div class="homi-title">Tagihan Iuran</div>
            <div class="homi-subtitle">Ditampilkan dan dikelompokkan berdasarkan bulan (period).</div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
            <a href="{{ route('admin.fees.qr.index') }}"
               class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-gray-200 text-sm hover:bg-gray-50">
                Kelola QR
            </a>
            <a href="{{ route('admin.fees.invoices.create') }}"
               class="w-full sm:w-auto text-center px-4 py-2 rounded-lg bg-[var(--homi-orange)] text-white text-sm hover:bg-orange-500">
                + Buat Tagihan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mt-4 p-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mt-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter periode --}}
    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="text-xs text-gray-600">Tahun</label>
            <input type="number" name="year" min="2000" max="2100"
                   value="{{ request('year') }}"
                   class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="text-xs text-gray-600">Bulan</label>
            <select name="month" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach($monthNames as $num => $label)
                    <option value="{{ $num }}" {{ (string)request('month')===(string)$num ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs text-gray-600">Status</label>
            <select name="status" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach(['unpaid'=>'Belum Bayar','paid'=>'Sudah Bayar','pending'=>'Menunggu','rejected'=>'Ditolak'] as $k=>$v)
                    <option value="{{ $k }}" {{ request('status')===$k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col sm:flex-row items-end gap-2">
            <button class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                Terapkan
            </button>
            <a href="{{ route('admin.fees.invoices.index') }}"
               class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-gray-200 text-sm hover:bg-gray-50">
                Reset
            </a>
        </div>
    </form>
</div>

@php
    $groups = $items->getCollection()->groupBy(function($it){
        if (!$it->period) return 'tanpa';
        try {
            return \Illuminate\Support\Carbon::parse($it->period)->format('Y-m');
        } catch (\Throwable $e) {
            return 'tanpa';
        }
    });
@endphp

<div class="mt-6 space-y-5">
    @forelse($groups as $key => $rows)
        @php
            $label = 'Tanpa Periode';
            if ($key !== 'tanpa') {
                [$yy, $mm] = explode('-', $key);
                $label = ($monthNames[(int)$mm] ?? $mm) . ' ' . $yy;
            }
        @endphp

        <div class="homi-card">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="font-semibold text-gray-800">{{ $label }}</div>
                <div class="text-xs text-gray-500">Jumlah: {{ $rows->count() }}</div>
            </div>

            {{-- MOBILE: CARDS --}}
            <div class="mt-3 space-y-3 md:hidden">
                @foreach($rows as $it)
                    @php
                        $pLabel = $it->period
                            ? (function() use ($it, $monthNames){
                                $c = \Illuminate\Support\Carbon::parse($it->period);
                                return ($monthNames[(int)$c->format('m')] ?? $c->format('m')) . ' ' . $c->format('Y');
                              })()
                            : '-';

                        $status = $it->status ?? '-';
                        $badge = match($status){
                            'unpaid'   => 'bg-amber-100 text-amber-800',
                            'paid'     => 'bg-emerald-100 text-emerald-800',
                            'pending'  => 'bg-sky-100 text-sky-800',
                            'rejected' => 'bg-rose-100 text-rose-800',
                            default    => 'bg-gray-100 text-gray-700'
                        };
                    @endphp

                    <div class="rounded-xl border border-[var(--homi-border)] bg-white p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-xs text-gray-500">ID: {{ $it->id }}</div>
                                <div class="font-semibold text-gray-900 break-words">
                                    {{ optional($it->user)->full_name ?? optional($it->user)->name ?? ('User #'.$it->user_id) }}
                                </div>
                                <div class="text-[11px] text-gray-500">User ID: {{ $it->user_id }}</div>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $status }}</span>
                        </div>

                        <div class="mt-3 text-sm space-y-1">
                            <div><span class="text-gray-500 text-[11px]">Jenis:</span> <span class="text-gray-800">{{ optional($it->feeType)->name ?? ('FeeType #'.$it->fee_type_id) }}</span></div>
                            <div><span class="text-gray-500 text-[11px]">Periode:</span> <span class="text-gray-800">{{ $pLabel }}</span></div>
                            <div><span class="text-gray-500 text-[11px]">Nominal:</span> <span class="font-semibold text-gray-900">Rp {{ number_format((int)$it->amount, 0, ',', '.') }}</span></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- DESKTOP: TABLE --}}
            <div class="mt-3 overflow-x-auto hidden md:block">
                <table class="homi-table min-w-[900px] w-full">
                    <thead>
                        <tr>
                            <th class="text-left">ID</th>
                            <th class="text-left">Warga</th>
                            <th class="text-left">Jenis</th>
                            <th class="text-left">Periode</th>
                            <th class="text-right">Nominal</th>
                            <th class="text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $it)
                            @php
                                $pLabel = $it->period
                                    ? (function() use ($it, $monthNames){
                                        $c = \Illuminate\Support\Carbon::parse($it->period);
                                        return ($monthNames[(int)$c->format('m')] ?? $c->format('m')) . ' ' . $c->format('Y');
                                      })()
                                    : '-';

                                $status = $it->status ?? '-';
                                $badge = match($status){
                                    'unpaid'   => 'bg-amber-100 text-amber-800',
                                    'paid'     => 'bg-emerald-100 text-emerald-800',
                                    'pending'  => 'bg-sky-100 text-sky-800',
                                    'rejected' => 'bg-rose-100 text-rose-800',
                                    default    => 'bg-gray-100 text-gray-700'
                                };
                            @endphp

                            <tr>
                                <td>{{ $it->id }}</td>
                                <td>
                                    <div class="font-medium text-gray-800">
                                        {{ optional($it->user)->full_name ?? optional($it->user)->name ?? ('User #'.$it->user_id) }}
                                    </div>
                                    <div class="text-xs text-gray-500">User ID: {{ $it->user_id }}</div>
                                </td>
                                <td>{{ optional($it->feeType)->name ?? ('FeeType #'.$it->fee_type_id) }}</td>
                                <td>{{ $pLabel }}</td>
                                <td class="text-right">Rp {{ number_format((int)$it->amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="homi-card text-sm text-gray-600">
            Belum ada data tagihan.
        </div>
    @endforelse
</div>

<div class="mt-5">
    {{ $items->links() }}
</div>
@endsection
