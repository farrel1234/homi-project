@extends('layouts.app')

@section('title','Laporan Pengaduan')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
<div class="space-y-8 py-4">
    {{-- Header & Stats --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase">Laporan Pengaduan</h1>
            <p class="text-slate-500 font-medium">Monitoring dan tindak lanjut keluhan warga Hawai Garden</p>
        </div>
        
        <div class="flex items-center gap-4 bg-white p-2 rounded-[2rem] shadow-sm border border-slate-100">
            <div class="px-6 py-2 bg-slate-50 rounded-full border border-slate-100">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block leading-none">Total Laporan</span>
                <span class="text-lg font-black text-slate-900">{{ $items->total() }}</span>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="homi-card p-6 overflow-visible border-none shadow-xl shadow-slate-200/50">
        <form action="{{ route('complaints.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-center">
            <div class="relative w-full lg:w-72 group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[var(--homi-blue)] transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari perihal atau pelapor..." 
                       class="homi-input pl-12 font-bold">
            </div>

            <div class="w-full lg:w-56 group">
                <select name="status" class="homi-input font-black uppercase tracking-widest text-xs">
                    <option value="">SEMUA STATUS</option>
                    <option value="baru" @selected(request('status')=='baru')>LAPORAN BARU</option>
                    <option value="diproses" @selected(request('status')=='diproses')>DALAM PROSES</option>
                    <option value="selesai" @selected(request('status')=='selesai')>SUDAH SELESAI</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full lg:w-auto">
                <button type="submit" class="flex-1 lg:flex-none px-8 py-3.5 rounded-2xl bg-slate-900 text-white text-xs font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] transition-all">
                    Filter
                </button>
                @if(request('status') || request('q'))
                    <a href="{{ route('complaints.index') }}" class="px-4 py-3.5 rounded-2xl bg-slate-100 text-slate-500 hover:text-rose-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Main Content --}}
    <div class="homi-card p-0 overflow-hidden border-none shadow-2xl shadow-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 italic">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Perihal Pengaduan</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Data Pelapor</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Masuk Pada</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($items as $c)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-black text-slate-900 leading-tight mb-1 max-w-sm">
                                    {{ $c->perihal ?? 'Tanpa Perihal' }}
                                </div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em]">ID: #CP-{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-slate-400 group-hover:bg-white transition-colors">
                                        {{ strtoupper(substr($c->nama_pelapor ?? ($c->user->full_name ?? $c->user->name ?? '?'), 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-700 leading-none mb-1">
                                            {{ $c->nama_pelapor ?? ($c->user->full_name ?? $c->user->name ?? $c->user->username ?? '-') }}
                                        </div>
                                        <div class="text-[11px] font-bold text-slate-400">
                                            {{ $c->user->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-100
                                    @if($c->status === 'selesai') bg-emerald-50 text-emerald-600 border-emerald-100
                                    @elseif($c->status === 'diproses') bg-amber-50 text-amber-600 border-amber-100
                                    @else bg-slate-100 text-slate-500 @endif">
                                    {{ match($c->status) { 'baru' => 'BARU', 'diproses' => 'DIPROSES', 'selesai' => 'SELESAI', default => '-' } }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-900 leading-none mb-1">{{ optional($c->created_at)->format('d M Y') }}</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ optional($c->created_at)->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('complaints.edit', $c->id) }}" 
                                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-white border border-slate-200 text-[10px] font-black text-slate-700 uppercase tracking-widest hover:border-[var(--homi-blue)] hover:text-[var(--homi-blue)] hover:shadow-lg hover:shadow-slate-100 transition-all">
                                        Proses
                                    </a>
                                    <form action="{{ route('complaints.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus laporan pengaduan ini?')">
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
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada pengaduan yang masuk</p>
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


</div>
@endsection
