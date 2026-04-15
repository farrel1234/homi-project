@extends('layouts.app')

@section('title', 'Tagihan Iuran')

@section('content')
<div class="space-y-8 py-4">
    {{-- Header & Stats --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase">Tagihan Iuran</h1>
            <p class="text-slate-500 font-medium">Monitoring dan kelola tagihan periodik warga {{ session('tenant_name', 'Homi') }}</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.fees.qr.index') }}" 
               class="px-6 py-3 rounded-2xl bg-white border border-slate-200 text-[10px] font-black text-slate-700 uppercase tracking-widest hover:border-[var(--homi-blue)] hover:text-[var(--homi-blue)] transition-all">
                Kelola QRIS
            </a>
            <a href="{{ route('admin.fees.invoices.create') }}" 
               class="px-6 py-3 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] hover:shadow-xl hover:shadow-blue-500/20 transition-all">
                + Buat Tagihan
            </a>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="homi-card p-6 overflow-visible border-none shadow-xl shadow-slate-200/50">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tahun</label>
                <input type="number" name="year" min="2000" max="2100" value="{{ request('year') }}" 
                       class="homi-input font-bold">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Bulan</label>
                <select name="month" class="homi-input font-bold text-xs">
                    <option value="">SEMUA BULAN</option>
                    @foreach($monthNames as $num => $label)
                        <option value="{{ $num }}" @selected((string)request('month')===(string)$num)>{{ strtoupper($label) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status</label>
                <select name="status" class="homi-input font-bold text-xs uppercase">
                    <option value="">SEMUA STATUS</option>
                    @foreach(['unpaid'=>'Belum Bayar','paid'=>'Sudah Bayar','pending'=>'Menunggu','rejected'=>'Ditolak'] as $k=>$v)
                        <option value="{{ $k }}" @selected(request('status')===$k)>{{ strtoupper($v) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-3.5 rounded-2xl bg-slate-100 text-slate-900 text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                    Terapkan
                </button>
                <a href="{{ route('admin.fees.invoices.index') }}" class="px-4 py-3.5 rounded-2xl bg-slate-50 text-slate-400 hover:text-rose-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Bulk Actions Bar --}}
    <div class="homi-card p-4 border-none shadow-lg shadow-slate-100/50 flex flex-col sm:flex-row items-center justify-between gap-4 sticky top-4 z-10 bg-white/90 backdrop-blur-md">
        <label class="flex items-center gap-3 cursor-pointer group">
            <div class="relative flex items-center">
                <input type="checkbox" id="select-all-invoices" class="w-5 h-5 rounded-lg border-2 border-slate-200 text-[var(--homi-blue)] focus:ring-[var(--homi-blue)] focus:ring-offset-0 transition-all cursor-pointer">
            </div>
            <span class="text-xs font-black text-slate-500 uppercase tracking-widest group-hover:text-slate-700 transition-colors">Pilih Semua di Halaman Ini</span>
        </label>

        <div class="flex items-center gap-4">
            <span id="selected-count" class="text-[10px] font-black text-[var(--homi-blue)] bg-blue-50 px-4 py-2 rounded-full uppercase tracking-widest">0 Terpilih</span>
            <button type="button" id="bulk-delete-btn" class="px-6 py-2.5 rounded-xl bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-widest border border-rose-100 hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm">
                Hapus Terpilih
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.fees.invoices.bulk-destroy') }}" id="bulk-delete-form" class="hidden">
        @csrf
        <div id="bulk-delete-inputs"></div>
    </form>

    {{-- Main Content (Grouped) --}}
    <div class="space-y-8">
        @forelse($groups as $key => $rows)
            @php
                $label = 'Tanpa Periode';
                if ($key !== 'tanpa') {
                    [$yy, $mm] = explode('-', $key);
                    $label = ($monthNames[(int)$mm] ?? $mm) . ' ' . $yy;
                }
                $groupKey = (string) $key;
            @endphp
            <div class="space-y-4">
                <div class="flex items-center gap-4 px-2">
                    <h3 class="text-lg font-black text-slate-900 uppercase italic tracking-tight">{{ $label }}</h3>
                    <div class="h-px flex-1 bg-slate-100"></div>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" class="w-4 h-4 rounded border-slate-200 text-[var(--homi-blue)] focus:ring-0 transition-all select-group" data-group="{{ $groupKey }}">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-600">Pilih Bulan Ini</span>
                    </label>
                </div>

                <div class="homi-card p-0 overflow-hidden border-none shadow-2xl shadow-slate-200">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100 italic">
                                    <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] w-12">#</th>
                                    <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Data Warga</th>
                                    <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Jenis / Deskripsi</th>
                                    <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Nominal</th>
                                    <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                                    <th class="px-8 py-5 text-right text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Kontrol</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($rows as $it)
                                    @php
                                        $status = $it->status ?? '-';
                                        $badge = match($status){
                                            'unpaid'   => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'paid'     => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'pending'  => 'bg-sky-50 text-sky-600 border-sky-100',
                                            'rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
                                            default    => 'bg-slate-100 text-slate-500 border-slate-200'
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/30 transition-colors group">
                                        <td class="px-8 py-5">
                                            <input type="checkbox" class="w-5 h-5 rounded-lg border-2 border-slate-200 text-[var(--homi-blue)] focus:ring-0 transition-all cursor-pointer invoice-check" 
                                                   data-group="{{ $groupKey }}" value="{{ $it->id }}">
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="flex-1">
                                                    <div class="font-black text-slate-900 leading-none mb-1">
                                                        {{ optional($it->user)->full_name ?? optional($it->user)->name ?? ('User #'.$it->user_id) }}
                                                    </div>
                                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ID: #{{ $it->id }}</div>
                                                </div>
                                                @if(isset($it->nb_delinquent) && $it->nb_delinquent)
                                                    <div class="group relative">
                                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700 text-[8px] font-black uppercase tracking-tighter border border-amber-200 cursor-help">
                                                            NB RISK
                                                        </span>
                                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-48 p-2 bg-slate-900 text-white text-[9px] font-bold rounded shadow-xl z-50">
                                                            Warga diprediksi berpotensi menunggak ({{ round($it->nb_prob * 100) }}% probability) berdasarkan profil & histori.
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 font-bold text-slate-600 text-xs italic">
                                            {{ optional($it->feeType)->name ?? ('Iuran #'.$it->fee_type_id) }}
                                        </td>
                                        <td class="px-8 py-5 text-right font-black text-slate-900 text-sm italic">
                                            Rp {{ number_format((int)$it->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $badge }}">
                                                {{ strtoupper($status) }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <form method="POST" action="{{ route('admin.fees.invoices.destroy', $it->id) }}" onsubmit="return confirm('Hapus tagihan ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2.5 rounded-xl bg-white border border-slate-100 text-slate-300 hover:border-rose-200 hover:text-rose-500 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-20 text-center homi-card border-none shadow-2xl shadow-slate-200/50">
                <div class="flex flex-col items-center justify-center space-y-4 text-slate-300">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada data tagihan</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="mt-8">
            {{ $items->onEachSide(1)->links() }}
        </div>
    @endif
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all-invoices');
    const groupChecks = Array.from(document.querySelectorAll('.select-group'));
    const itemChecks = Array.from(document.querySelectorAll('.invoice-check'));
    const countEl = document.getElementById('selected-count');
    const bulkBtn = document.getElementById('bulk-delete-btn');
    const bulkForm = document.getElementById('bulk-delete-form');
    const bulkInputs = document.getElementById('bulk-delete-inputs');

    function uniqueSelectedIds() {
        const ids = new Set();
        itemChecks.forEach((el) => {
            if (el.checked) ids.add(String(el.value));
        });
        return Array.from(ids);
    }

    function refreshState() {
        const ids = uniqueSelectedIds();
        if (countEl) countEl.textContent = `${ids.length} dipilih`;

        if (selectAll) {
            const allChecked = itemChecks.length > 0 && itemChecks.every((el) => el.checked);
            selectAll.checked = allChecked;
        }

        groupChecks.forEach((groupBox) => {
            const group = groupBox.dataset.group;
            const members = itemChecks.filter((el) => el.dataset.group === group);
            groupBox.checked = members.length > 0 && members.every((el) => el.checked);
        });
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            itemChecks.forEach((el) => { el.checked = selectAll.checked; });
            refreshState();
        });
    }

    groupChecks.forEach((groupBox) => {
        groupBox.addEventListener('change', function () {
            const group = groupBox.dataset.group;
            itemChecks
                .filter((el) => el.dataset.group === group)
                .forEach((el) => { el.checked = groupBox.checked; });
            refreshState();
        });
    });

    itemChecks.forEach((el) => {
        el.addEventListener('change', refreshState);
    });

    if (bulkBtn && bulkForm && bulkInputs) {
        bulkBtn.addEventListener('click', function () {
            const ids = uniqueSelectedIds();
            if (!ids.length) {
                alert('Pilih minimal satu tagihan.');
                return;
            }

            if (!confirm(`Hapus ${ids.length} tagihan terpilih?`)) {
                return;
            }

            bulkInputs.innerHTML = '';
            ids.forEach((id) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                bulkInputs.appendChild(input);
            });

            bulkForm.submit();
        });
    }

    refreshState();
});
</script>
@endsection
