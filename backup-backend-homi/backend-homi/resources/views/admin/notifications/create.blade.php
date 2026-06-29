@extends('layouts.app')

@section('title','Kirim Notifikasi')

@php
    // Label period supaya orang awam ngerti (contoh: 2026-06 => Juni 2026)
    $periodLabel = null;
    $oldPeriod = old('period');
    if ($oldPeriod) {
        try {
            $periodLabel = \Carbon\Carbon::createFromFormat('Y-m', $oldPeriod)->translatedFormat('F Y');
        } catch (\Throwable $e) {
            $periodLabel = null;
        }
    }
@endphp

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="homi-card p-0 overflow-hidden shadow-2xl shadow-slate-200 border-none">
        {{-- Header --}}
        <div class="bg-slate-900 p-8 text-white relative">
            <div class="absolute right-0 top-0 p-8 opacity-10">
                <svg viewBox="0 0 24 24" class="h-24 w-24 fill-current"><path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-2xl font-black tracking-tight mb-2 uppercase">Kirim Notifikasi</h1>
                <p class="text-slate-400 text-sm font-medium">Kirim pesan langsung ke aplikasi mobile warga</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.notifications.store') }}" class="p-8 space-y-8">
            @csrf

            {{-- Row 1: Penerima --}}
            <div class="space-y-2">
                <label class="homi-label text-slate-900">Pilih Target Warga</label>
                <select name="user_id" class="homi-input font-bold">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>
                            {{ $u->full_name ?? $u->name ?? $u->username ?? '-' }} ({{ $u->email ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Row 2: Judul & Tipe --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="homi-label text-slate-900">Judul Pesan</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Subject notifikasi..." 
                           class="homi-input font-bold" required>
                </div>
                <div class="space-y-2">
                    <label class="homi-label text-slate-900">Jenis Notifikasi</label>
                    <select name="type" class="homi-input font-bold">
                        @php $t = old('type', 'general'); @endphp
                        <option value="general"  @selected($t==='general')>Umum / Informasi</option>
                        <option value="invoice"  @selected($t==='invoice')>Penagihan Iuran</option>
                        <option value="announcement" @selected($t==='announcement')>Pengumuman Baru</option>
                        <option value="complaint" @selected($t==='complaint')>Update Pengaduan</option>
                    </select>
                </div>
            </div>

            {{-- Row 3: Pesan --}}
            <div class="space-y-2">
                <label class="homi-label text-slate-900">Isi Notifikasi</label>
                <textarea name="message" rows="4" placeholder="Tuliskan pesan singkat yang akan muncul di layar HP warga..." 
                          class="homi-input" required>{{ old('message') }}</textarea>
            </div>

            {{-- Row 4: Aksi & Detail --}}
            <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 shadow-inner space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="homi-label text-slate-500">Tujuan Halaman (Opsional)</label>
                        <select name="route" class="homi-input text-xs font-black uppercase tracking-widest">
                            @php $r = old('route', 'TagihanIuran'); @endphp
                            <option value="" @selected($r==='')>Hanya Notifikasi</option>
                            <option value="TagihanIuran" @selected($r==='TagihanIuran')>Halaman Tagihan</option>
                            <option value="PengaduanWarga" @selected($r==='PengaduanWarga')>Halaman Pengaduan</option>
                            <option value="Beranda" @selected($r==='Beranda')>Beranda App</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="homi-label text-slate-500">Periode Terkait (Jika Ada)</label>
                        <div class="relative">
                            <input type="text" name="period" value="{{ old('period') }}" placeholder="YYYY-MM" 
                                   class="homi-input font-mono">
                            @if($periodLabel)
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-[var(--homi-blue)] uppercase">
                                    {{ $periodLabel }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="homi-label text-slate-500">Nomor Invoice Terkait (Opsional)</label>
                    <input type="number" name="invoice_id" value="{{ old('invoice_id') }}" placeholder="ID Invoice" 
                           class="homi-input">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse sm:flex-row justify-end items-center gap-4 pt-8 border-t border-slate-100">
                <a href="{{ route('admin.notifications.index') }}" class="w-full sm:w-auto text-center px-10 py-3 rounded-2xl text-sm font-black text-slate-400 uppercase tracking-[0.2em] hover:text-slate-600 transition-colors">
                    Kembali
                </a>
                <button type="submit" class="w-full sm:w-auto px-12 py-4 rounded-[1.5rem] bg-slate-900 text-white text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:shadow-[var(--homi-blue-light)] hover:bg-[var(--homi-blue)] transition-all">
                    Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

    </div>
</div>
@endsection
