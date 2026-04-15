@extends('layouts.app')

@section('title','Data Unit')
@section('page_title','Data Unit')
@section('page_subtitle','Kelola unit hunian dan blok perumahan')

@section('content')
<div class="space-y-8 py-4">
    {{-- Header & Stats --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase italic">Data Unit</h1>
            <p class="text-slate-500 font-medium">Manajemen blok dan nomor rumah warga {{ session('tenant_name', 'Homi') }}</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="px-6 py-2 bg-white rounded-full border border-slate-100 shadow-sm">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block leading-none">Total Unit</span>
                <span class="text-lg font-black text-slate-900">{{ $units->total() }}</span>
            </div>
            <a href="{{ route('units.create') }}" 
               class="px-6 py-3.5 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] hover:shadow-xl hover:shadow-blue-500/20 transition-all">
                + Tambah Unit
            </a>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="homi-card p-0 overflow-hidden border-none shadow-2xl shadow-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 italic">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] w-20">No</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kode Unit</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Blok / Lokasi</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Lantai</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($units as $unit)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6 font-black text-slate-300 italic">
                                {{ str_pad(($units->currentPage() - 1) * $units->perPage() + $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-8 py-6 uppercase">
                                <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-900 font-black text-xs tracking-widest border border-slate-100 group-hover:bg-white group-hover:border-[var(--homi-blue)] transition-all">
                                    {{ $unit->code }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-black text-slate-700 leading-none mb-1">{{ $unit->block ?? 'Umum' }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">{{ session('tenant_name', 'Perumahan') }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-[10px] font-black text-slate-500 border border-slate-100 group-hover:bg-white transition-colors">
                                    {{ $unit->floor ?? '1' }}
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('units.edit', $unit->id) }}" 
                                       class="p-2.5 rounded-xl bg-white border border-slate-100 text-slate-400 hover:border-[var(--homi-blue)] hover:text-[var(--homi-blue)] transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus unit {{ $unit->code }}?')">
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
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4 text-slate-300">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada data unit hunian</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($units->hasPages())
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
                {{ $units->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
