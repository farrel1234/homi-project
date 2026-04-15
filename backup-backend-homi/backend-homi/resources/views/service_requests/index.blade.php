@extends('layouts.app')

@section('title','Pengajuan Layanan / Surat')

@section('content')
<div class="space-y-6">

<div class="space-y-8 py-4">
    {{-- Header & Stats --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 px-4 sm:px-0">
        <div class="space-y-1 text-center md:text-left">
            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 uppercase">Daftar Pengajuan</h1>
            <p class="text-slate-500 text-sm font-medium">Kelola surat menyurat dan permohonan layanan warga</p>
        </div>
        
        <div class="flex items-center justify-center md:justify-end gap-4 bg-white p-2 rounded-[2rem] shadow-sm border border-slate-100 mx-auto md:mx-0">
            <div class="px-6 py-2 bg-slate-50 rounded-full border border-slate-100">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block leading-none">Total Pengajuan</span>
                <span class="text-lg font-black text-slate-900">{{ $items->total() }}</span>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="homi-card p-4 sm:p-6 overflow-visible border-none shadow-xl shadow-slate-200/50">
        <form action="{{ route('service-requests.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative w-full md:w-72 group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[var(--homi-blue)] transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau jenis..." 
                       class="homi-input pl-12 font-bold">
            </div>

            <div class="w-full md:w-56 group">
                <select name="status" class="homi-input font-black uppercase tracking-widest text-xs">
                    <option value="">SEMUA STATUS</option>
                    <option value="submitted" @selected($status=='submitted')>DIAJUKAN</option>
                    <option value="processed" @selected($status=='processed')>DIPROSES</option>
                    <option value="approved" @selected($status=='approved')>DISETUJUI</option>
                    <option value="rejected" @selected($status=='rejected')>DITOLAK</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none px-8 py-3.5 rounded-2xl bg-slate-900 text-white text-xs font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] transition-all">
                    Filter
                </button>
                @if($status || $q)
                    <a href="{{ route('service-requests.index') }}" class="px-4 py-3.5 rounded-2xl bg-slate-100 text-slate-500 hover:text-rose-500 transition-colors">
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
                        <th class="px-4 sm:px-5 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap">Warga & Kontak</th>
                        <th class="px-4 sm:px-5 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap">Jenis Pengajuan</th>
                        <th class="px-4 sm:px-5 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap hidden md:table-cell">Subjek / Detail</th>
                        <th class="px-4 sm:px-5 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap">Status</th>
                        <th class="px-4 sm:px-5 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap hidden sm:table-cell">Waktu</th>
                        <th class="px-4 sm:px-5 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($items as $req)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-4 sm:px-5 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-slate-100 items-center justify-center font-black text-slate-400 group-hover:bg-white group-hover:text-[var(--homi-blue)] transition-colors">
                                        {{ strtoupper(substr($req->user?->full_name ?? $req->user?->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-900 leading-none mb-1 text-sm sm:text-base">
                                            {{ $req->user?->full_name ?? $req->user?->name ?? $req->user?->username ?? 'User Tidak Ditemukan' }}
                                        </div>
                                        <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                            {{ $req->user?->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 sm:px-5 py-6">
                                <div class="font-black text-slate-700 leading-none mb-1 text-xs sm:text-sm">{{ $req->type->name ?? 'Layanan Umum' }}</div>
                                <div class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em]">ID: #{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-4 sm:px-5 py-6 hidden md:table-cell">
                                <div class="max-w-[150px] lg:max-w-[200px] truncate font-medium text-slate-600 italic text-sm">
                                    "{{ $req->subject ?? $req->title ?? 'Tidak ada subjek' }}"
                                </div>
                            </td>
                            <td class="px-4 sm:px-5 py-6">
                                <span class="px-3 sm:px-4 py-1.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-widest border border-slate-100 whitespace-nowrap
                                    @if($req->status === 'approved') bg-emerald-50 text-emerald-600 border-emerald-100
                                    @elseif($req->status === 'rejected') bg-rose-50 text-rose-600 border-rose-100
                                    @elseif($req->status === 'processed') bg-amber-50 text-amber-600 border-amber-100
                                    @else bg-slate-100 text-slate-500 @endif">
                                    {{ $req->status_label ?? $req->status }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-8 py-6 hidden sm:table-cell">
                                <div class="font-bold text-slate-900 leading-none mb-1 text-xs sm:text-sm">{{ optional($req->created_at)->format('d M Y') }}</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ optional($req->created_at)->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-4 sm:px-5 py-6 text-right">
                                <a href="{{ route('service-requests.show', $req->id) }}" 
                                   class="inline-flex items-center gap-2 px-3 sm:px-5 py-2 rounded-xl bg-white border border-slate-200 text-[10px] font-black text-slate-700 uppercase tracking-widest hover:border-[var(--homi-blue)] hover:text-[var(--homi-blue)] hover:shadow-lg hover:shadow-slate-100 transition-all">
                                    <span class="hidden lg:inline">Detail</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </div>
                                    <p class="font-black text-slate-300 uppercase tracking-[0.2em] text-xs">Belum ada pengajuan masuk</p>
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
