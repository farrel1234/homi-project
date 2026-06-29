@extends('layouts.app')

@section('title', 'QR Iuran')
@section('page_title', 'QR Iuran')
@section('page_subtitle', 'Kelola QR aktif untuk pembayaran iuran')

@php
    function qr_src($row) {
        $url = $row->qr_url ?? $row->url ?? null;
        if ($url) return $url;

        $path = $row->image_path ?? $row->qr_image_path ?? $row->qr_path ?? $row->path ?? null;
        if (!$path) return null;

        $p = str_replace('\\', '/', $path);
        $p = ltrim($p, '/');

        if (preg_match('/^https?:\/\//i', $p)) return $p;

        $p = preg_replace('#^public/#', '', $p);
        $p = preg_replace('#^storage/#', '', $p);

        return asset('storage/'.$p);
    }

    function qr_src_bust($row) {
        $src = qr_src($row);
        if (!$src) return null;
        $v = optional($row->updated_at)->timestamp ?? optional($row->created_at)->timestamp ?? time();
        return $src . (str_contains($src, '?') ? '&' : '?') . 'v=' . $v;
    }
@endphp

@section('content')

<div class="space-y-8 py-4">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase italic">Konfigurasi QRIS</h1>
            <p class="text-slate-500 font-medium">Atur kode QR pembayaran iuran warga {{ session('tenant_name', 'Homi') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- QR Aktif Panel --}}
        <div class="lg:col-span-12">
            <div class="homi-card p-0 overflow-hidden border-none shadow-2xl shadow-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-8 md:p-12 bg-slate-900 flex flex-col items-center justify-center text-center space-y-6">
                        @php $srcActive = $active ? qr_src_bust($active) : null; @endphp
                        @if($active && $srcActive)
                            <div class="relative group">
                                <div class="absolute -inset-4 bg-[var(--homi-blue)] rounded-[2.5rem] blur-2xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                                <div class="relative bg-white p-4 rounded-[2rem] shadow-2xl border-4 border-slate-800">
                                    <img src="{{ $srcActive }}" alt="QR Aktif" class="w-64 h-64 object-contain rounded-xl">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <span class="px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                                    STATUS: QR AKTIF
                                </span>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">ID: #QR-{{ str_pad($active->id, 3, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        @else
                            <div class="w-64 h-64 rounded-[2rem] bg-slate-800 border-2 border-dashed border-slate-700 flex flex-col items-center justify-center text-slate-500 space-y-4">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                <p class="text-[10px] font-black uppercase tracking-widest">Belum Ada QR Aktif</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-8 md:p-12 bg-white space-y-8 flex flex-col justify-center">
                        <div class="space-y-2">
                            <h2 class="text-2xl font-black text-slate-900 uppercase">Input QR Baru</h2>
                            <p class="text-slate-500 text-sm leading-relaxed">Upload gambar QRIS statis kompleks Anda. Sistem akan menggunakan QR ini sebagai tujuan pembayaran utama pada setiap tagihan warga yang dibuat.</p>
                        </div>

                        <form action="{{ route('admin.fees.qr.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Pilih File Gambar (Maks 5MB)</label>
                                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 group-hover:border-[var(--homi-blue)] transition-colors">
                                    <input type="file" name="qr_image" class="absolute inset-0 opacity-0 cursor-pointer" required>
                                    <span class="text-xs font-bold text-slate-400 italic">Drag & drop atau klik untuk cari...</span>
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-4 rounded-2xl bg-slate-900 text-white text-xs font-black uppercase tracking-[0.2em] hover:bg-[var(--homi-blue)] hover:shadow-xl hover:shadow-blue-500/20 transition-all active:scale-[0.98]">
                                Simpan & Aktifkan QR
                            </button>
                        </form>
                        
                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-[11px] text-amber-700 font-medium leading-relaxed italic">Pastikan QRIS yang diupload adalah QRIS statis yang tidak memiliki nominal dan belum kedaluwarsa.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- QR History --}}
        <div class="lg:col-span-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-px flex-1 bg-slate-100"></div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Riwayat Koleksi QR</h3>
                <div class="h-px flex-1 bg-slate-100"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($items as $it)
                    @php
                        $src = qr_src_bust($it);
                        $isActive = (int)($it->is_active ?? 0) === 1;
                    @endphp
                    <div class="homi-card p-6 border-none shadow-xl shadow-slate-200/50 group hover:-translate-y-1 transition-all duration-300">
                        <div class="relative mb-6 flex justify-center">
                            @if($isActive)
                                <div class="absolute -top-3 -right-3 z-10 w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            @endif
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 group-hover:border-[var(--homi-blue)] transition-colors">
                                <img src="{{ $src }}" class="w-32 h-32 object-contain rounded-lg opacity-80 group-hover:opacity-100 transition-opacity" />
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="text-center">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Upload Pada</div>
                                <div class="font-bold text-slate-900 italic text-sm">{{ optional($it->created_at)->format('d M Y') ?? '-' }}</div>
                            </div>
                            
                            <div class="flex flex-col gap-2">
                                @if(!$isActive)
                                    <form action="{{ route('admin.fees.qr.activate', $it->id) }}" method="POST">
                                        @csrf
                                        <button class="w-full py-2.5 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] transition-all">
                                            Aktifkan
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.fees.qr.destroy', $it->id) }}" method="POST" onsubmit="return confirm('Hapus QR ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="w-full py-2.5 rounded-xl bg-white border border-slate-100 text-rose-500 text-[10px] font-black uppercase tracking-widest hover:bg-rose-50 hover:border-rose-100 transition-all">
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <div class="py-2.5 rounded-xl bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100 text-center">
                                        SEDANG AKTIF
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center homi-card border-none shadow-xl shadow-slate-200/50">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">Belum Ada Riwayat QR</p>
                    </div>
                @endforelse
            </div>
            
            @if($items->hasPages())
                <div class="mt-8">
                    {{ $items->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
