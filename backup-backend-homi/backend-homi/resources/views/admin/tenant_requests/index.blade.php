@extends('layouts.app')

@section('title', 'Permintaan Trial')
@section('page_title', 'Trial Inquiries')
@section('page_subtitle', 'Kelola pendaftaran perumahan baru')

@section('content')
<div class="space-y-8 py-4">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase italic">Permintaan Trial</h1>
            <p class="text-slate-500 font-medium">Daftar calon pelanggan yang tertarik mencoba sistem HOMI</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="homi-card p-0 overflow-hidden border-none shadow-2xl shadow-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 italic">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-r border-slate-100">Info Perumahan</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-r border-slate-100">Kontak Pengelola</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-r border-slate-100">Status</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($requests as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6 border-r border-slate-100">
                                <div class="font-black text-slate-900 leading-none mb-1">{{ $item->name }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">TGL: {{ $item->created_at->format('d M Y') }}</div>
                                @if($item->notes)
                                    <div class="mt-2 text-[10px] text-slate-500 italic bg-slate-100 p-2 rounded-lg border-l-2 border-slate-300">
                                        "{{ $item->notes }}"
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-6 border-r border-slate-100">
                                <div class="font-bold text-slate-700 text-sm italic">{{ $item->manager_name }}</div>
                                <div class="flex flex-col gap-1 mt-1">
                                    <a href="mailto:{{ $item->email }}" class="text-[10px] font-black tracking-widest text-blue-500 hover:underline uppercase">{{ $item->email }}</a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->phone) }}" target="_blank" class="text-[10px] font-black tracking-widest text-emerald-500 hover:underline uppercase">WA: {{ $item->phone }}</a>
                                </div>
                            </td>
                            <td class="px-8 py-6 border-r border-slate-100">
                                @if($item->status === 'pending')
                                    <span class="px-4 py-1.5 rounded-full bg-amber-50 text-amber-600 font-black text-[10px] tracking-widest border border-amber-100 uppercase italic">
                                        Menunggu
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 font-black text-[10px] tracking-widest border border-emerald-100 uppercase italic">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-400 font-black text-[10px] tracking-widest border border-slate-200 uppercase italic">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2 text-decoration-none">
                                    @if($item->status === 'pending')
                                        <form action="{{ route('tenant-requests.approve', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-[var(--homi-blue)] transition-all">
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M20 6L9 17l-5-5"/></svg>
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('tenant-requests.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus permintaan ini?')">
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
                                    <p class="font-black uppercase tracking-[0.2em] text-xs">Belum ada permintaan trial masuk</p>
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
