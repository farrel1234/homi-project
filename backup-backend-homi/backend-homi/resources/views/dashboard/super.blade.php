@extends('layouts.app')

@section('title', 'Super Dashboard')
@section('page_title', 'Homi Global Overview')
@section('page_subtitle', 'Monitoring entitas perumahan & permintaan trial')

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="homi-card relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all duration-500"></div>
            <div class="relative flex items-center gap-6">
                <div class="h-16 w-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform duration-500">
                    <svg viewBox="0 0 24 24" class="h-8 w-8 fill-none stroke-current stroke-2"><path d="M3 21h18M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM9 9h6M9 13h6"/></svg>
                </div>
                <div>
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Perumahan</h4>
                    <p class="text-4xl font-extrabold text-slate-900 tracking-tight leading-none mt-1">{{ $totalTenants }}</p>
                </div>
            </div>
        </div>

        <div class="homi-card relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
            <div class="relative flex items-center gap-6">
                <div class="h-16 w-16 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-500">
                    <svg viewBox="0 0 24 24" class="h-8 w-8 fill-none stroke-current stroke-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Tenant Aktif</h4>
                    <p class="text-4xl font-extrabold text-slate-900 tracking-tight leading-none mt-1">{{ $activeTenants }}</p>
                </div>
            </div>
        </div>

        <div class="homi-card relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-500/10 rounded-full blur-2xl group-hover:bg-orange-500/20 transition-all duration-500"></div>
            <div class="relative flex items-center gap-6">
                <div class="h-16 w-16 bg-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-200 group-hover:scale-110 transition-transform duration-500">
                    <svg viewBox="0 0 24 24" class="h-8 w-8 fill-none stroke-current stroke-2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Permintaan Trial</h4>
                    <p class="text-4xl font-extrabold text-slate-900 tracking-tight leading-none mt-1">{{ $pendingRequests }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        {{-- RECENT REQUESTS --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1 bg-indigo-500 rounded-full"></div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Permintaan Trial Terbaru</h2>
                </div>
                <a href="{{ route('tenant-requests.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 group">
                    Lihat Semua
                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2 group-hover:translate-x-1 transition-transform"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentRequests as $req)
                    <div class="homi-card !p-4 flex items-center justify-between group/item border-l-4 border-l-transparent hover:border-l-indigo-500">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-2xl bg-indigo-100 flex items-center justify-center font-black text-indigo-700 text-lg shadow-inner group-hover/item:bg-indigo-600 group-hover/item:text-white transition-all duration-300">
                                {{ substr($req->name, 0, 1) }}
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-bold text-slate-900 leading-tight group-hover/item:text-indigo-600 transition-colors">{{ $req->name }}</h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $req->manager_name }}</span>
                                    <span class="h-1 w-1 bg-slate-300 rounded-full"></span>
                                    <span class="text-[10px] font-medium text-slate-400">{{ $req->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            @if($req->status === 'pending')
                                <a href="{{ route('tenant-requests.approve', $req->id) }}" 
                                   class="px-5 py-2 rounded-xl bg-slate-900 text-white text-[10px] font-bold uppercase tracking-widest hover:bg-indigo-600 hover:shadow-lg hover:shadow-indigo-200 transition-all">
                                    Approve
                                </a>
                            @else
                                <span class="px-4 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest border border-emerald-100 italic">
                                    {{ $req->status }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-16 rounded-[2rem] bg-white/50 border-2 border-dashed border-slate-200 text-center backdrop-blur-sm">
                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300 mb-4">
                            <svg viewBox="0 0 24 24" class="h-8 w-8 fill-none stroke-current stroke-2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Tidak ada permintaan baru</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RECENT TENANTS --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1 bg-emerald-500 rounded-full"></div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Tenant Baru Bergabung</h2>
                </div>
                <a href="{{ route('tenants.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 group">
                    Kelola Master
                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2 group-hover:translate-x-1 transition-transform"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentTenants as $t)
                    <div class="homi-card !p-4 group/tenant overflow-hidden border-l-4 border-l-transparent hover:border-l-emerald-500">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 min-w-[48px] rounded-2xl bg-slate-50 shadow-sm border border-slate-100 flex items-center justify-center font-black text-emerald-600 text-xs group-hover/tenant:scale-110 transition-transform">
                                    {{ $t->code }}
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-900 leading-tight group-hover/tenant:text-emerald-600 transition-colors">{{ $t->name }}</h4>
                                    <div class="flex items-center gap-2">
                                        <code class="text-[9px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">DB: {{ $t->db_database }}</code>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest border border-emerald-100/50">
                                    {{ $t->plan }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <div class="relative flex h-2 w-2">
                                        @if($t->is_active)
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        @else
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-300"></span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $t->is_active ? 'Active' : 'Offline' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-16 rounded-[2rem] bg-white/50 border-2 border-dashed border-slate-200 text-center backdrop-blur-sm">
                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300 mb-4">
                            <svg viewBox="0 0 24 24" class="h-8 w-8 fill-none stroke-current stroke-2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Belum ada tenant</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
