@extends('layouts.app')

@section('title', 'Permintaan Trial')
@section('page_title', 'Trial Inquiries')
@section('page_subtitle', 'Kelola pendaftaran perumahan baru')

@section('content')
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Permintaan Trial</h1>
            <p class="text-slate-500 font-medium">Daftar calon pelanggan yang tertarik mencoba sistem HOMI</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-2xl bg-white/50 border border-slate-200 backdrop-blur-sm shadow-sm flex items-center gap-3">
                <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">{{ $requests->where('status', 'pending')->count() }} Pending</span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="homi-card !p-0 overflow-hidden border-none shadow-xl shadow-slate-200/50">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Informasi Perumahan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Kontak Pengelola</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-right text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $item)
                        <tr class="hover:bg-slate-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1">
                                    <span class="font-bold text-slate-900 text-base group-hover:text-indigo-600 transition-colors">{{ $item->name }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Diajukan {{ $item->created_at->format('d M Y') }}</span>
                                    </div>
                                    @if($item->notes)
                                        <div class="mt-3 text-[11px] text-slate-600 leading-relaxed bg-slate-100/50 p-3 rounded-xl border border-slate-200/50 italic">
                                            "{{ $item->notes }}"
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-2 text-slate-700 font-bold text-sm">
                                        <div class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-400">
                                            <svg viewBox="0 0 24 24" class="h-3 w-3 fill-none stroke-current stroke-2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </div>
                                        {{ $item->manager_name }}
                                    </div>
                                    <div class="flex flex-col gap-1 pl-8">
                                        <a href="mailto:{{ $item->email }}" class="text-[10px] font-bold text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-wider">{{ $item->email }}</a>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->phone) }}" target="_blank" class="text-[10px] font-bold text-emerald-500 hover:text-emerald-600 transition-colors uppercase tracking-wider flex items-center gap-1">
                                            <span>WA: {{ $item->phone }}</span>
                                            <svg viewBox="0 0 24 24" class="h-3 w-3 fill-none stroke-current stroke-2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if($item->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-50 text-amber-600 font-bold text-[10px] tracking-widest border border-amber-100/50 uppercase">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-[10px] tracking-widest border border-emerald-100/50 uppercase">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 text-slate-400 font-bold text-[10px] tracking-widest border border-slate-200/50 uppercase">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-3">
                                    @if($item->status === 'pending')
                                        <form action="{{ route('tenant-requests.approve', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:shadow-lg hover:shadow-indigo-200 transition-all">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('tenant-requests.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus permintaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 rounded-xl bg-white border border-slate-100 text-slate-300 hover:border-rose-200 hover:text-rose-500 hover:bg-rose-50/30 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4 text-slate-300">
                                    <div class="h-16 w-16 rounded-2xl bg-slate-50 flex items-center justify-center">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <p class="font-bold uppercase tracking-[0.2em] text-[10px] text-slate-400">Belum ada permintaan trial masuk</p>
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
