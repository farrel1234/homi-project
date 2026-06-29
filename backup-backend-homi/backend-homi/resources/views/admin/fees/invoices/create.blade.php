@extends('layouts.app')

@section('title', 'Buat Tagihan Iuran')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="homi-card p-0 overflow-hidden shadow-2xl shadow-slate-200 border-none">
        {{-- Header --}}
        <div class="bg-slate-900 p-8 text-white relative">
            <div class="absolute right-0 top-0 p-8 opacity-10">
                <svg viewBox="0 0 24 24" class="h-24 w-24 fill-current"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-2xl font-black tracking-tight mb-2 uppercase">Buat Tagihan Iuran</h1>
                <p class="text-slate-400 text-sm font-medium">Generate tagihan bulanan secara massal atau personal</p>
            </div>
        </div>

        <form action="{{ route('admin.fees.invoices.store') }}" method="POST" class="p-8 space-y-8">
            @csrf

            {{-- Row 1: Tipe & Nominal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="homi-label text-slate-900">Jenis Iuran</label>
                    <select name="fee_type_id" class="homi-input font-bold">
                        @foreach($feeTypes as $t)
                            <option value="{{ $t->id }}">{{ $t->name ?? ('Iuran #'.$t->id) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="homi-label text-slate-900">Nominal (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-slate-400">Rp</span>
                        <input type="number" name="amount" min="1" value="{{ old('amount', 50000) }}" 
                               class="homi-input pl-12 font-black text-lg">
                    </div>
                </div>
            </div>

            {{-- Row 2: Periode --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="homi-label">Tahun</label>
                    <input type="number" name="tahun" min="2000" max="2100" value="{{ old('tahun', date('Y')) }}" 
                           class="homi-input font-bold text-center">
                </div>
                <div class="space-y-2">
                    <label class="homi-label">Dari Bulan</label>
                    <select name="bulan_mulai" class="homi-input font-bold">
                        @foreach($monthNames as $num => $label)
                            <option value="{{ $num }}" @selected((int)old('bulan_mulai', 1) === $num)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="homi-label">Sampai Bulan</label>
                    <select name="bulan_sampai" class="homi-input font-bold">
                        @foreach($monthNames as $num => $label)
                            <option value="{{ $num }}" @selected((int)old('bulan_sampai', 12) === $num)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row 3: Target --}}
            <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 shadow-inner space-y-6">
                <div class="space-y-2">
                    <label class="homi-label">Target Penerima</label>
                    <select name="target" id="target" class="homi-input font-black uppercase tracking-widest text-xs">
                        <option value="all" @selected(old('target','all')==='all')>SELURUH WARGA {{ strtoupper($tenant->name ?? 'WARGA') }}</option>
                        <option value="one" @selected(old('target')==='one')>SATU WARGA SPESIFIK</option>
                    </select>
                </div>

                <div id="userWrap" class="{{ old('target')==='one' ? '' : 'hidden' }} space-y-2 animate-in slide-in-from-top-2 duration-300">
                    <label class="homi-label">Pilih Warga</label>
                    <select name="user_id" class="homi-input font-bold">
                        <option value="">-- Cari Nama Warga --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" @selected((string)old('user_id')===(string)$u->id)>
                                {{ $u->full_name ?? $u->name ?? ('Warga #'.$u->id) }} 
                                (Blok {{ $u->blok ?? '?' }} No. {{ $u->no_rumah ?? '?' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row 4: QR Status & Actions --}}
            <div class="flex flex-col-reverse sm:flex-row justify-between items-center gap-6 pt-8 border-t border-slate-100">
                <div class="flex items-center gap-3">
                    @if(isset($qr) && $qr && $qr->display_url)
                        <div class="flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full border border-emerald-100 text-[10px] font-black uppercase tracking-widest">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            QRIS Aktif Terdeteksi
                        </div>
                    @else
                        <div class="flex items-center gap-2 bg-rose-50 text-rose-700 px-4 py-2 rounded-full border border-rose-100 text-[10px] font-black uppercase tracking-widest">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            QRIS Belum Diatur ⚠️
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <a href="{{ route('admin.fees.invoices.index') }}" class="flex-1 sm:flex-none text-center px-8 py-3 rounded-2xl text-sm font-black text-slate-400 uppercase tracking-[0.2em] hover:text-slate-600 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 sm:flex-none px-12 py-4 rounded-[1.5rem] bg-[var(--homi-orange)] text-white text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-orange-100 hover:shadow-orange-200 hover:bg-orange-600 transition-all">
                        Generate Invoice
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const target = document.getElementById('target');
    const wrap   = document.getElementById('userWrap');
    function sync() {
        if (target.value === 'one') wrap.classList.remove('hidden');
        else wrap.classList.add('hidden');
    }
    target.addEventListener('change', sync);
    sync();
});
</script>
@endpush
@endsection
