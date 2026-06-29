@extends('layouts.app')

@section('title', 'Data Tenant')
@section('page_title', 'Manajemen Tenant')
@section('page_subtitle', 'Kelola entitas perumahan/komplek')

@section('content')
<div class="space-y-8 py-4">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 text-xs font-bold flex items-center gap-3">
            <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M20 6L9 17l-5-5"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold flex items-center gap-3">
            <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase italic">Master Tenant</h1>
            <p class="text-slate-500 font-medium">Pengaturan entitas perumahan dan komplek hunian aktif</p>
        </div>
        
        <a href="{{ route('tenants.create') }}" 
           class="px-6 py-3.5 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] hover:shadow-xl hover:shadow-blue-500/20 transition-all text-center">
            + Tambah Tenant
        </a>
    </div>

    {{-- Main Content --}}
    <div class="homi-card p-0 overflow-hidden border-none shadow-2xl shadow-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 italic">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-r border-slate-100">Identitas Tenant</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-r border-slate-100">Registration Code</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-r border-slate-100">Database Info</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-r border-slate-100">Domain / URL</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6 border-r border-slate-100">
                                <div class="font-black text-slate-900 leading-none mb-1">{{ $item->name }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">KODE: {{ $item->code }}</div>
                            </td>
                            <td class="px-8 py-6 border-r border-slate-100">
                                <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-black text-[10px] tracking-widest border border-slate-100 group-hover:bg-white group-hover:border-[var(--homi-blue)] transition-all">
                                    {{ $item->registration_code ?? '---' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 border-r border-slate-100">
                                <div class="flex flex-col gap-2">
                                    <span class="font-mono text-[10px] text-slate-500 font-bold bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                                        DB: {{ $item->db_database }}
                                    </span>
                                    <form action="{{ route('tenants.migrate', $item->id) }}" method="POST" onsubmit="return confirm('Jalankan migrasi pada database {{ $item->db_database }}?')">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] transition-all">
                                            Setup DB
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-xs font-bold text-slate-500 italic border-r border-slate-100">
                                {{ $item->domain ?? 'N/A' }}
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.tenants.switch', $item->id) }}" 
                                       title="Masuk ke Dashboard Perumahan"
                                       class="p-2.5 rounded-xl bg-white border border-slate-100 text-emerald-500 hover:border-emerald-500 hover:bg-emerald-50 transition-all no-underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    </a>
                                    <a href="{{ route('tenants.edit', $item->id) }}" 
                                       class="p-2.5 rounded-xl bg-white border border-slate-100 text-slate-400 hover:border-[var(--homi-blue)] hover:text-[var(--homi-blue)] transition-all no-underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('tenants.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tenant {{ $item->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 rounded-xl bg-white border border-slate-100 text-slate-300 hover:border-rose-200 hover:text-rose-500 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4 text-slate-300">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada entitas tenant terdaftar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
