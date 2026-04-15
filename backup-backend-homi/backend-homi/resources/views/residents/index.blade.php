@extends('layouts.app')

@section('title','Direktori Warga')

@section('content')
<div class="space-y-8 py-4">
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase italic">Direktori Warga</h1>
            <p class="text-slate-500 font-medium">Manajemen database penghuni secara terpusat & premium</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <form action="{{ route('residents.index') }}" method="GET" class="relative group w-full sm:w-auto">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="CARI NAMA / BLOK..." 
                       class="w-full sm:w-80 pl-6 pr-14 py-4 rounded-[2rem] bg-white border-2 border-slate-100 text-[11px] font-black uppercase tracking-widest focus:border-[var(--homi-blue)] focus:ring-0 transition-all placeholder:text-slate-300 shadow-sm shadow-slate-100/50">
                <button type="submit" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-hover:text-[var(--homi-blue)] transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>
            
            <a href="{{ route('residents.create') }}" 
               class="w-full sm:w-auto px-8 py-4 rounded-[2rem] bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-[var(--homi-blue)] hover:shadow-2xl hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95 text-center">
                + Tambah Warga
            </a>
        </div>
    </div>

    @if(session('ok'))
        <div class="space-y-4 animate-fade-in-down">
            <div class="p-4 rounded-3xl bg-emerald-50 text-emerald-700 text-sm border-2 border-emerald-100 font-black uppercase tracking-widest flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                {{ session('ok') }}
            </div>

            @if(session('plain_password'))
                <div class="p-6 rounded-[2.5rem] bg-slate-900 text-white shadow-2xl shadow-blue-500/20 border border-slate-800 relative overflow-hidden group">
                    {{-- Decorative --}}
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-[var(--homi-blue)] rounded-full opacity-20 blur-3xl group-hover:opacity-40 transition-opacity"></div>
                    
                    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-[var(--homi-blue)] flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                                </div>
                                <h4 class="text-xs font-black uppercase tracking-[0.2em] text-blue-400">Login Access Generated</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-12">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Email Username</p>
                                    <p class="text-sm font-black tracking-tight text-white">{{ session('new_resident_email') }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Temporary Password</p>
                                    <div class="flex items-center gap-3">
                                        <p class="text-sm font-black tracking-tight text-[var(--homi-orange)] font-mono">{{ session('plain_password') }}</p>
                                        <button onclick="navigator.clipboard.writeText('{{ session('plain_password') }}')" class="p-1 rounded bg-slate-800 text-slate-400 hover:text-white transition-colors">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="text-[10px] font-bold text-slate-500 italic max-w-[200px] leading-relaxed">
                                Silakan salin info login ini untuk diberikan kepada warga. Info password ini hanya muncul <span class="text-white">sekali saja</span>.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Grid Content --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse(($items ?? $residents ?? []) as $r)
            @php
                $u = $r->user ?? null;
                $nama = $u ? ($u->full_name ?? $u->name ?? $u->username) : 'Warga';
                $email = $u->email ?? '-';
                $username = $u->username ?? '-';
                
                // Variation of avatar color based on initial
                $initial = strtoupper(substr((string)$nama, 0, 1));
                $hue = (ord($initial) % 26) * 13.84; 
                $avatarBg = "hsl({$hue}, 70%, 94%)";
                $avatarText = "hsl({$hue}, 70%, 40%)";
            @endphp
            <div class="group relative bg-white rounded-[2.5rem] border border-slate-100 p-6 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:shadow-slate-300/60 hover:-translate-y-2 transition-all duration-500 overflow-hidden">
                {{-- Decorative element --}}
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full opacity-[0.03] group-hover:scale-150 transition-transform duration-700" style="background-color: {{ $avatarText }}"></div>
                
                <div class="relative flex flex-col items-center text-center space-y-4">
                    {{-- Avatar --}}
                    <div class="relative">
                        <div class="w-20 h-20 rounded-[2rem] flex items-center justify-center font-black text-2xl transition-all duration-500 group-hover:rounded-2xl" 
                             style="background-color: {{ $avatarBg }}; color: {{ $avatarText }};">
                            {{ $initial }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-lg border-4 border-white flex items-center justify-center shadow-lg {{ ($r->is_public ?? false) ? 'bg-emerald-500' : 'bg-slate-300' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="space-y-1">
                        <h3 class="font-black text-slate-900 group-hover:text-[var(--homi-blue)] transition-colors">{{ $nama }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic truncate max-w-[180px]">{{ $email }}</p>
                    </div>

                    {{-- Badges --}}
                    <div class="flex items-center gap-2 pt-2">
                        <div class="px-3 py-1.5 rounded-xl bg-slate-50 text-slate-600 border border-slate-100 text-[10px] font-black tracking-widest leading-none group-hover:bg-slate-900 group-hover:text-white transition-all">
                            BLOK {{ $r->block ?? $r->blok ?? '-' }}
                        </div>
                        <div class="px-3 py-1.5 rounded-xl bg-slate-50 text-slate-600 border border-slate-100 text-[10px] font-black tracking-widest leading-none group-hover:bg-[var(--homi-orange)] group-hover:text-white transition-all">
                            NO {{ $r->house_number ?? $r->no_rumah ?? '-' }}
                        </div>
                    </div>

                    {{-- Username & Status --}}
                    <div class="flex items-center justify-between w-full pt-4 mt-2 border-t border-slate-50">
                        <div class="text-[10px] font-bold text-slate-400 italic">@ {{ $username }}</div>
                        <div class="text-[9px] font-black uppercase tracking-widest {{ ($r->is_public ?? false) ? 'text-emerald-500' : 'text-slate-400' }}">
                            {{ ($r->is_public ?? false) ? 'Publik' : 'Private' }}
                        </div>
                    </div>

                    {{-- Controls --}}
                    <div class="flex items-center gap-2 pt-2 w-full">
                        <a href="{{ route('residents.edit', $r->id) }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-widest border border-transparent hover:border-[var(--homi-blue)] hover:text-[var(--homi-blue)] hover:bg-white transition-all text-decoration-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M11 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        <form action="{{ route('residents.destroy', $r->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data warga {{ $nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2.5 rounded-xl bg-slate-50 text-slate-300 hover:text-rose-500 hover:bg-rose-100 transition-all border-none cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="flex flex-col items-center justify-center py-32 space-y-6 text-slate-300">
                    <div class="p-8 rounded-[3rem] bg-slate-50 border-2 border-dashed border-slate-100">
                        <svg class="w-24 h-24 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="font-black uppercase tracking-[0.3em] text-xs mb-2">Data Belum Tersedia</p>
                        <p class="text-[10px] font-bold text-slate-400">Silakan tambahkan warga baru untuk mengisi direktori ini</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @php $paginator = $items ?? $residents ?? null; @endphp
    @if($paginator && method_exists($paginator, 'links'))
        <div class="flex justify-center pt-8">
            <div class="bg-white p-2 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                {{ $paginator->onEachSide(1)->links() }}
            </div>
        </div>
    @endif
</div>

<style>
@keyframes fade-in-down {
    0% { opacity: 0; transform: translateY(-10px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down {
    animation: fade-in-down 0.5s ease-out;
}
</style>
@endsection
