@extends('layouts.app')

@section('title','Pengajuan Surat')

@section('content')
<div class="space-y-8 py-4">
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase italic">Arsip Surat</h1>
            <p class="text-slate-500 font-medium">Manajemen dokumen dan surat resmi Hawai Garden</p>
        </div>
        
        <form action="{{ route('letter-requests.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <select name="status" class="w-full sm:w-auto pl-5 pr-10 py-3.5 rounded-2xl bg-white border-2 border-slate-100 text-[10px] font-black uppercase tracking-widest focus:border-[var(--homi-blue)] focus:ring-0 transition-all">
                <option value="">SEMUA STATUS</option>
                <option value="submitted" @selected($status=='submitted')>DIAJUKAN</option>
                <option value="processed" @selected($status=='processed')>DIPROSES</option>
                <option value="approved" @selected($status=='approved')>DISETUJUI</option>
                <option value="rejected" @selected($status=='rejected')>DITOLAK</option>
            </select>
            
            <div class="relative group w-full sm:w-64">
                <input type="text" name="q" value="{{ $q }}" placeholder="CARI WARGA / JENIS..." 
                       class="w-full pl-5 pr-12 py-3.5 rounded-2xl bg-white border-2 border-slate-100 text-[10px] font-black uppercase tracking-widest focus:border-[var(--homi-blue)] focus:ring-0 transition-all placeholder:text-slate-300">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-hover:text-[var(--homi-blue)] transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </form>
    </div>

    {{-- Main Content --}}
    <div class="homi-card p-0 overflow-hidden border-none shadow-2xl shadow-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 italic">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Data Pemohon</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Spesifikasi Surat</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status Verifikasi</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Tanggal Masuk</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($items as $req)
                        @php
                            $u = $req->user;
                            $name = $u?->full_name ?? $u?->username ?? 'Warga';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[var(--homi-blue)]/10 text-[var(--homi-blue)] flex items-center justify-center font-black text-xs">
                                        {{ strtoupper(substr($name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-900 leading-none mb-1">{{ $name }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">{{ $u?->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-black text-slate-800 text-xs italic">{{ $req->type->name ?? 'TIPE TIDAK DIKENAL' }}</div>
                                <div class="text-[9px] font-black text-slate-300 uppercase tracking-[0.15em] mt-0.5">ID: {{ $req->id }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest italic shadow-sm {{ $req->status_badge_class }}">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="font-black text-slate-900 text-sm italic">{{ $req->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic leading-none">{{ $req->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('letter-requests.show', $req->id) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] transition-all">
                                    Detail
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4 text-slate-300">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada pengajuan surat yang masuk</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($items->hasPages())
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
                {{ $items->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
