@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
<div class="space-y-8 py-4">
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase italic">Pengumuman</h1>
            <p class="text-slate-500 font-medium">Broadcast informasi penting untuk seluruh warga {{ session('tenant_name', 'Homi') }}</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <form action="{{ route('announcements.index') }}" method="GET" class="relative group w-full sm:w-auto">
                <input type="text" name="q" value="{{ $q ?? request('q') }}" placeholder="CARI JUDUL..." 
                       class="w-full sm:w-64 pl-5 pr-12 py-3.5 rounded-2xl bg-white border-2 border-slate-100 text-[10px] font-black uppercase tracking-widest focus:border-[var(--homi-blue)] focus:ring-0 transition-all placeholder:text-slate-300">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-hover:text-[var(--homi-blue)] transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>
            <a href="{{ route('announcements.create') }}" 
               class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] hover:shadow-xl hover:shadow-blue-500/20 transition-all text-center">
                + Buat Baru
            </a>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($announcements as $item)
            <div class="homi-card p-0 flex flex-col group border-none shadow-xl shadow-slate-200/50 hover:-translate-y-1 transition-all duration-300">
                <div class="relative h-48 overflow-hidden rounded-t-[2rem]">
                    @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="Cover" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002-2z"/></svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1.5 rounded-full {{ $item->is_public ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }} text-[9px] font-black uppercase tracking-widest shadow-lg">
                            {{ $item->is_public ? 'PUBLIK' : 'DRAFT' }}
                        </span>
                    </div>
                    <div class="absolute bottom-4 left-6">
                        <span class="px-3 py-1 rounded-lg bg-white/20 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-widest border border-white/30">
                            {{ $item->category ?? 'UMUM' }}
                        </span>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col space-y-4">
                    <div class="space-y-2">
                        <h3 class="text-base font-black text-slate-900 leading-tight uppercase line-clamp-2 italic group-hover:text-[var(--homi-blue)] transition-colors">
                            {{ $item->title }}
                        </h3>
                        <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $item->created_at?->format('d M Y') }}
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 flex items-center justify-between gap-3 mt-auto">
                        <form action="{{ route('announcements.toggle-active', $item) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2.5 rounded-xl {{ $item->is_public ? 'bg-slate-100 text-slate-500 hover:bg-slate-200' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white' }} text-[10px] font-black uppercase tracking-widest transition-all">
                                {{ $item->is_public ? 'Arsipkan' : 'Publikasikan' }}
                            </button>
                        </form>
                        
                        <div class="flex items-center gap-2">
                            <a href="{{ route('announcements.edit', $item) }}" 
                               class="p-2.5 rounded-xl bg-white border border-slate-100 text-slate-400 hover:border-[var(--homi-blue)] hover:text-[var(--homi-blue)] transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('announcements.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 rounded-xl bg-white border border-slate-100 text-slate-300 hover:border-rose-200 hover:text-rose-500 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center homi-card border-none shadow-2xl shadow-slate-200/50">
                <div class="flex flex-col items-center justify-center space-y-4 text-slate-300">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11 5h2M11 9h2m7 7v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1m-6 9l-2 2-2-2m2 2v-4"/></svg>
                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada pengumuman yang dibuat</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($announcements->hasPages())
        <div class="mt-12">
            {{ $announcements->onEachSide(1)->links() }}
        </div>
    @endif
</div>


@endsection
