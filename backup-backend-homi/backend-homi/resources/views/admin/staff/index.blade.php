@extends('layouts.app')

@section('title','Manajemen Staff')

@section('content')
<div class="space-y-8 py-4">
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase italic">Manajemen Staff</h1>
            <p class="text-slate-500 font-medium">Kelola administrator dan pengelola perumahan</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <form action="{{ route('admin.staff.index') }}" method="GET" class="relative group w-full sm:w-auto">
                <input type="text" name="q" value="{{ $q }}" placeholder="CARI NAMA / EMAIL..." 
                       class="w-full sm:w-64 pl-5 pr-12 py-3.5 rounded-2xl bg-white border-2 border-slate-100 text-[10px] font-black uppercase tracking-widest focus:border-[var(--homi-blue)] focus:ring-0 transition-all placeholder:text-slate-300">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-hover:text-[var(--homi-blue)] transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>

            <a href="{{ route('admin.staff.create') }}" 
               class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] hover:shadow-xl hover:shadow-blue-500/20 transition-all text-center">
                + Tambah Staff
            </a>
        </div>
    </div>

    @if(session('ok'))
        <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-700 text-sm border-2 border-emerald-100 font-bold">
            {{ session('ok') }}
        </div>
    @endif

    {{-- Main Content --}}
    <div class="homi-card p-0 overflow-hidden border-none shadow-2xl shadow-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 italic">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Profil Staff</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Username</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Role</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Perumahan</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center font-black text-xs group-hover:bg-[var(--homi-blue)] group-hover:text-white transition-all">
                                        {{ strtoupper(substr($item->full_name ?? $item->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-900 leading-none mb-1">{{ $item->full_name ?? $item->name }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">{{ $item->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs font-bold text-slate-500 italic">
                                    @ {{ $item->username }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                @if($item->isSuperAdmin())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-widest shadow-sm border border-rose-100">
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-sky-600 text-[9px] font-black uppercase tracking-widest border border-sky-100">
                                        Admin
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 uppercase tracking-widest font-black text-[9px]">
                                @if($item->isSuperAdmin())
                                    <span class="text-slate-300 italic">Semua Perumahan</span>
                                @else
                                    <span class="text-[var(--homi-blue)]">{{ $item->tenant->name ?? 'Belum Di-Set' }}</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2 text-decoration-none">
                                    <a href="{{ route('admin.staff.edit', $item->id) }}" 
                                       class="p-2.5 rounded-xl bg-white border border-slate-100 text-slate-400 hover:border-[var(--homi-blue)] hover:text-[var(--homi-blue)] transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if($item->id !== auth()->id())
                                    <form action="{{ route('admin.staff.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus staff {{ $item->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 rounded-xl bg-white border border-slate-100 text-slate-300 hover:border-rose-200 hover:text-rose-500 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4 text-slate-300">
                                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada data staff</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
