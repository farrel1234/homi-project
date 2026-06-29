@extends('layouts.app')

@section('title','Notifikasi')

@section('content')
<div class="space-y-8 py-4">
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase italic">Notifikasi</h1>
            <p class="text-slate-500 font-medium">Riwayat pesan broadcast dan personal ke aplikasi warga</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <form action="{{ route('admin.notifications.index') }}" method="GET" class="relative group w-full sm:w-auto">
                <input type="text" name="q" value="{{ $q ?? request('q') }}" placeholder="CARI WARGA / JUDUL..." 
                       class="w-full sm:w-64 pl-5 pr-12 py-3.5 rounded-2xl bg-white border-2 border-slate-100 text-[10px] font-black uppercase tracking-widest focus:border-[var(--homi-blue)] focus:ring-0 transition-all placeholder:text-slate-300">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-hover:text-[var(--homi-blue)] transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>
            <a href="{{ route('admin.notifications.create') }}" 
               class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] hover:shadow-xl hover:shadow-blue-500/20 transition-all text-center">
                + Kirim Pesan
            </a>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="homi-card p-0 overflow-hidden border-none shadow-2xl shadow-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 italic">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Penerima</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Konten Pesan</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] w-48 text-right">Waktu Kirim</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($items as $n)
                        @php
                            $u = $n->user;
                            $name = $u->full_name ?? $u->name ?? $u->username ?? 'Warga';
                            $isRead = !empty($n->read_at);
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[var(--homi-blue)]/10 text-[var(--homi-blue)] flex items-center justify-center font-black text-xs">
                                        {{ strtoupper(substr($name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-900 leading-none mb-1">{{ $name }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">{{ $u->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="space-y-1 max-w-md">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[8px] font-black uppercase tracking-widest italic">
                                            {{ $n->type ?? 'SYSTEM' }}
                                        </span>
                                        <div class="font-black text-slate-800 text-xs italic">{{ $n->title }}</div>
                                    </div>
                                    <div class="text-xs text-slate-500 line-clamp-1 italic">{{ $n->message }}</div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if($isRead)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        Dibaca
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 text-slate-400 border border-slate-100 text-[9px] font-black uppercase tracking-widest">
                                        <span class="w-2 h-2 rounded-full bg-slate-300 animate-pulse"></span>
                                        Terkirim
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="font-black text-slate-900 text-sm italic">{{ optional($n->created_at)->format('d M Y') }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic leading-none">{{ optional($n->created_at)->format('H:i') }} WIB</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4 text-slate-300">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada riwayat notifikasi</p>
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
