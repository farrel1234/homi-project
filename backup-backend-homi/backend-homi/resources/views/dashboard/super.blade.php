@extends('layouts.app')

@section('title', 'Super Dashboard')
@section('page_title', 'Homi Global Overview')
@section('page_subtitle', 'Monitoring entitas perumahan & permintaan trial')

@section('content')
<div class="space-y-8 py-4">
    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="homi-card p-6 bg-slate-900 border-none relative overflow-hidden group hover:shadow-[0_20px_50px_rgba(15,23,42,0.3)] transition-all duration-500">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-[var(--homi-blue)] rounded-full opacity-10 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative flex items-center gap-6">
                <div class="h-16 w-16 bg-slate-800 rounded-2xl flex items-center justify-center text-[var(--homi-blue)] shadow-inner">
                    <svg viewBox="0 0 24 24" class="h-8 w-8 fill-none stroke-current stroke-2"><path d="M3 21h18M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2zM9 9h6M9 13h6"/></svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] italic">Total Perumahan</h4>
                    <p class="text-4xl font-black text-white italic tracking-tighter">{{ $totalTenants }}</p>
                </div>
            </div>
        </div>

        <div class="homi-card p-6 bg-white border-2 border-slate-50 relative overflow-hidden group hover:border-[var(--homi-blue)] transition-all duration-300">
            <div class="relative flex items-center gap-6">
                <div class="h-16 w-16 bg-blue-50 rounded-2xl flex items-center justify-center text-[var(--homi-blue)]">
                    <svg viewBox="0 0 24 24" class="h-8 w-8 fill-none stroke-current stroke-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Tenant Aktif</h4>
                    <p class="text-4xl font-black text-slate-900 italic tracking-tighter">{{ $activeTenants }}</p>
                </div>
            </div>
        </div>

        <div class="homi-card p-6 bg-white border-2 border-slate-50 relative overflow-hidden group hover:border-orange-400 transition-all duration-300">
            @if($pendingRequests > 0)
                <div class="absolute top-4 right-4 h-3 w-3 bg-orange-500 rounded-full animate-ping"></div>
            @endif
            <div class="relative flex items-center gap-6">
                <div class="h-16 w-16 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-500">
                    <svg viewBox="0 0 24 24" class="h-8 w-8 fill-none stroke-current stroke-2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Permintaan Trial</h4>
                    <p class="text-4xl font-black text-slate-900 italic tracking-tighter">{{ $pendingRequests }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- RECENT REQUESTS --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-900 uppercase tracking-widest italic">Permintaan Trial Terbaru</h2>
                <a href="{{ route('tenant-requests.index') }}" class="text-[10px] font-black text-[var(--homi-blue)] uppercase tracking-widest hover:underline">Lihat Semua</a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentRequests as $req)
                    <div class="homi-card p-5 group hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-xl bg-slate-50 flex items-center justify-center font-black text-slate-900 text-xs border border-slate-100 group-hover:bg-[var(--homi-blue)] group-hover:text-white transition-all">
                                    {{ substr($req->name, 0, 1) }}
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="font-bold text-slate-900 leading-tight">{{ $req->name }}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $req->manager_name }} • {{ $req->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($req->status === 'pending')
                                    <a href="{{ route('tenant-requests.approve', $req->id) }}" 
                                       class="px-4 py-2 rounded-xl bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] transition-all">
                                        Approve
                                    </a>
                                @else
                                    <span class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest border border-emerald-100 italic">
                                        {{ $req->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-100 text-center">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Tidak ada permintaan trial baru</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RECENT TENANTS --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-900 uppercase tracking-widest italic">Tenant Baru Bergabung</h2>
                <a href="{{ route('tenants.index') }}" class="text-[10px] font-black text-[var(--homi-blue)] uppercase tracking-widest hover:underline">Kelola Master</a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentTenants as $t)
                    <div class="homi-card p-5 group hover:-translate-y-1 transition-all duration-300 bg-slate-50/50">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 min-w-[40px] rounded-lg bg-white shadow-sm flex items-center justify-center font-black text-[var(--homi-blue)] text-[10px] border border-slate-100">
                                    {{ $t->code }}
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="font-bold text-slate-900 leading-tight">{{ $t->name }}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 font-mono uppercase tracking-widest">DB: {{ $t->db_database }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="px-3 py-1 rounded-lg bg-white text-slate-400 text-[8px] font-bold uppercase tracking-widest border border-slate-100 italic">
                                    Plan: {{ strtoupper($t->plan) }}
                                </span>
                                <div class="flex items-center gap-1">
                                    <div class="h-2 w-2 rounded-full {{ $t->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $t->is_active ? 'Active' : 'Offline' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-100 text-center">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Belum ada tenant terdaftar</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
