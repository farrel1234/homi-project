@extends('layouts.app')

@section('title','Dashboard Admin')

@section('page_title','Dashboard')
@section('page_subtitle','Panel Kendali Utama')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">

    {{-- 1. WELCOME SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
                Selamat Datang, {{ auth()->user()->full_name ?? auth()->user()->name ?? 'Admin' }}! 👋
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Hari ini adalah {{ now()->translatedFormat('l, d F Y') }}. Berikut adalah ringkasan aktivitas di {{ session('tenant_name', 'perumahan') }}.
            </p>
        </div>
        <div class="flex items-center gap-2">
             <div class="px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm flex items-center gap-2 text-sm font-medium text-slate-600">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Sistem Online
             </div>
        </div>
    </div>

    {{-- 2. QUICK ACTIONS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <a href="{{ route('announcements.create') }}" class="group p-4 bg-white/80 backdrop-blur-sm border border-slate-200 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-[var(--homi-blue)] transition-all duration-300 flex flex-col gap-3">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-[var(--homi-blue)] flex items-center justify-center group-hover:bg-[var(--homi-blue)] group-hover:text-white transition-colors shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            </div>
            <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Pengumuman</span>
        </a>
        <a href="{{ route('residents.create') }}" class="group p-4 bg-white/80 backdrop-blur-sm border border-slate-200 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-emerald-500 transition-all duration-300 flex flex-col gap-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-colors shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Tambah Warga</span>
        </a>
        <a href="{{ route('admin.fees.invoices.create') }}" class="group p-4 bg-white/80 backdrop-blur-sm border border-slate-200 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-orange-500 transition-all duration-300 flex flex-col gap-3">
            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition-colors shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Tagihan Baru</span>
        </a>
        <a href="{{ route('admin.notifications.create') }}" class="group p-4 bg-white/80 backdrop-blur-sm border border-slate-200 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-purple-500 transition-all duration-300 flex flex-col gap-3">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-500 group-hover:text-white transition-colors shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            </div>
            <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Broadcast</span>
        </a>
        <a href="{{ route('payments.index') }}" class="group hidden lg:flex p-4 bg-white/80 backdrop-blur-sm border border-slate-200 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-slate-400 transition-all duration-300 flex flex-col gap-3">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-600 flex items-center justify-center group-hover:bg-slate-700 group-hover:text-white transition-colors shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Validasi</span>
        </a>
    </div>

    {{-- 3. STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        
        {{-- Card 1: Warga --}}
        <div class="relative overflow-hidden bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 group hover:shadow-2xl transition-all duration-500">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-sky-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="flex flex-col gap-4 relative">
                <div class="w-14 h-14 rounded-2xl bg-sky-100 text-[var(--homi-blue)] flex items-center justify-center shadow-lg shadow-sky-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Populasi Warga</p>
                    <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ number_format($totalResidents ?? 0) }}</p>
                </div>
            </div>
            <div class="mt-6 flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50 w-fit px-3 py-1 rounded-full border border-emerald-100">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Aktif Terdaftar
            </div>
        </div>

        {{-- Card 2: Pengumuman --}}
        <div class="relative overflow-hidden bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 group hover:shadow-2xl transition-all duration-500">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-purple-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="flex flex-col gap-4 relative">
                <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center shadow-lg shadow-purple-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Info & Berita</p>
                    <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ number_format($totalAnnouncements ?? 0) }}</p>
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('announcements.index') }}" class="text-[11px] font-black text-purple-600 uppercase tracking-widest hover:text-purple-800 flex items-center gap-1 group/link">
                    Kelola Konten 
                    <svg class="w-3 h-3 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Card 3: Pending Payments --}}
        <div class="relative overflow-hidden bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 group hover:shadow-2xl transition-all duration-500">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-orange-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="flex flex-col gap-4 relative">
                <div class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center shadow-lg shadow-orange-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Menunggu Review</p>
                    <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ $pendingPaymentsCount ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rp {{ number_format($totalPendingAmount ?? 0, 0, ',', '.') }}</span>
                <a href="{{ route('payments.index') }}" class="text-[11px] font-black text-orange-600 uppercase tracking-widest hover:text-orange-800 flex items-center gap-1 group/link">
                    Update 
                    <svg class="w-3 h-3 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Card 4: Service Requests --}}
        <div class="relative overflow-hidden bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 group hover:shadow-2xl transition-all duration-500">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-rose-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="flex flex-col gap-4 relative">
                <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shadow-lg shadow-rose-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Request Layanan</p>
                    <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ $serviceRequestCount ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-6 flex items-center gap-2 text-xs font-bold text-rose-600 bg-rose-50 w-fit px-3 py-1 rounded-full border border-rose-100">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $serviceRequestsToday ?? 0 }} Baru Hari Ini
            </div>
        </div>

        {{-- Card 5: Complaints (Pengaduan) --}}
        <div class="relative overflow-hidden bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 group hover:shadow-2xl transition-all duration-500">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="flex flex-col gap-4 relative">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shadow-lg shadow-amber-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Pengaduan Warga</p>
                    <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ $complaintCount ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs font-bold text-amber-600 bg-amber-50 w-fit px-3 py-1 rounded-full border border-amber-100">
                    {{ $pendingComplaintsCount ?? 0 }} Pending
                </div>
                <a href="{{ route('complaints.index') }}" class="text-[11px] font-black text-amber-600 uppercase tracking-widest hover:text-amber-800">Review</a>
            </div>
        </div>
    </div>

    {{-- 4. MIDDLE SECTION: ANNOUNCEMENTS & LATEST ACTIVITY --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- LEFT: ANNNOUNCEMENTS --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="w-2 h-6 bg-[var(--homi-blue)] rounded-full"></span>
                    Pengumuman Utama
                </h2>
                <a href="{{ route('announcements.index') }}" class="text-sm font-bold text-[var(--homi-blue)] hover:underline">Lihat Semua</a>
            </div>

            @if(!empty($mainAnnouncement))
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden group">
                    <div class="grid grid-cols-1 md:grid-cols-5 h-full">
                        <div class="md:col-span-2 bg-slate-100 overflow-hidden h-48 md:h-auto">
                            @if(!empty($mainAnnouncement->image_path))
                                <img src="{{ asset('storage/' . $mainAnnouncement->image_path) }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="News">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-sky-400 to-sky-600">
                                    <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="md:col-span-3 p-6 flex flex-col justify-center">
                            <span class="px-2 py-1 bg-sky-50 text-[var(--homi-blue)] text-[10px] font-black uppercase rounded-lg mb-2 inline-block w-fit tracking-wider">
                                Terbaru • {{ $mainAnnouncement->created_at?->format('d M Y') }}
                            </span>
                            <h3 class="text-xl font-bold text-slate-800 mb-3 leading-tight group-hover:text-[var(--homi-blue)] transition-colors">
                                {{ $mainAnnouncement->title }}
                            </h3>
                            <p class="text-slate-500 text-sm line-clamp-2 mb-4 leading-relaxed">
                                {{ \Illuminate\Support\Str::limit(strip_tags($mainAnnouncement->content), 180) }}
                            </p>
                            <a href="{{ route('announcements.show', $mainAnnouncement->id) }}" class="flex items-center gap-2 text-sm font-bold text-slate-900 group-hover:gap-3 transition-all">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 text-[var(--homi-orange)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-12 text-center">
                    <p class="text-slate-400 font-medium">Belum ada pengumuman yang dibuat.</p>
                </div>
            @endif

            {{-- MINI NEWS FEED --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach(($nextAnnouncements ?? []) as $item)
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 flex gap-4 hover:border-slate-300 transition-colors">
                        @if(!empty($item?->image_path))
                            <img src="{{ asset('storage/' . $item->image_path) }}" class="w-16 h-16 rounded-xl object-cover flex-shrink-0" alt="Thumb">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 text-slate-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-slate-400 block mb-1 uppercase tracking-tighter">{{ optional($item?->created_at)->format('d M Y') }}</span>
                            <h4 class="text-sm font-bold text-slate-800 truncate mb-1">{{ $item?->title }}</h4>
                            <p class="text-xs text-slate-500 line-clamp-1 leading-normal">{{ strip_tags($item?->content ?? '') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT: PAYMENTS & ARREARS --}}
        <div class="space-y-6">
            
            {{-- LATEST PAYMENTS --}}
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 overflow-hidden relative">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Pembayaran Baru</h2>
                    <a href="{{ route('payments.index') }}" class="text-xs font-bold text-[var(--homi-blue)] hover:underline">Semua</a>
                </div>

                <div class="space-y-4">
                    @forelse(($latestPayments ?? []) as $pay)
                        <div class="flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 font-black text-xs">
                                {{ strtoupper(substr($pay?->user?->full_name ?? 'W', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate leading-none mb-1">
                                    {{ $pay?->user?->full_name ?? $pay?->user?->username ?? 'Warga' }}
                                </p>
                                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-tighter">
                                    Rp {{ number_format($pay?->amount ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-[10px] font-bold text-slate-400 block mb-1 tracking-tighter">
                                    {{ optional($pay?->created_at)->format('H:i') }}
                                </p>
                                @php($st = strtolower($pay?->status ?? 'pending'))
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest
                                    {{ $st == 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $st == 'paid' ? 'LUNAS' : 'PENDING' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <p class="text-slate-400 text-sm">Belum ada transaksi.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ARREARS RISK ALERT --}}
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-800 rounded-3xl shadow-lg p-6 text-white overflow-hidden relative group">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-3xl group-hover:bg-white/10 transition-colors"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h2 class="text-sm font-black uppercase tracking-[0.2em] text-rose-400">AI PREDIKSI RISIKO</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div class="bg-white/5 border border-white/10 p-3 rounded-2xl">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Berisiko</p>
                            <p class="text-2xl font-black">{{ $arrearsSummary['at_risk_count'] ?? 0 }} <span class="text-xs font-medium text-slate-400">Warga</span></p>
                        </div>
                        <div class="bg-rose-500/10 border border-rose-500/20 p-3 rounded-2xl">
                            <p class="text-[10px] font-bold text-rose-300 uppercase tracking-widest mb-1">Kritis (AI)</p>
                            <p class="text-2xl font-black text-rose-400">{{ $arrearsSummary['high_risk_count'] ?? 0 }} <span class="text-xs font-medium text-rose-300/50">Warga</span></p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        @foreach(array_slice($arrearsSummary['top_arrears'] ?? [], 0, 3) as $row)
                            <div class="flex items-center justify-between gap-3 text-xs border-b border-white/5 pb-2">
                                <div class="min-w-0">
                                    <p class="font-bold truncate">{{ $row['name'] }}</p>
                                    <p class="text-[10px] text-slate-400">Blok {{ $row['blok'] }} No.{{ $row['no_rumah'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-black text-rose-400">Rp{{ number_format($row['amount'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ $arrearsSummary['cta_url'] }}" class="block w-full py-3 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl text-center text-xs font-black transition-colors">
                         {{ $arrearsSummary['cta_label'] }}
                    </a>
                </div>
            </div>


 {{-- PRIORITAS TUNGGAKAN --}}
<div class="bg-gradient-to-br from-red-500 to-red-600 border border-red-500 rounded-3xl shadow-lg p-6 text-white overflow-hidden relative group">

    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-colors"></div>

    <div class="relative">

        {{-- HEADER --}}
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 15l4-4 4 4 6-6"/>
                </svg>
            </div>

            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-white">
                Prioritas Tunggakan
            </h2>
        </div>

        {{-- ISI --}}
        <div class="space-y-3 text-sm mb-6">

            <div class="flex justify-between border-b border-white/10 pb-2">
                <span>Tinggi</span>
                <span class="font-black">{{ $prioritySummary['high'] ?? 0 }}</span>
            </div>

            <div class="flex justify-between border-b border-white/10 pb-2">
                <span>Sedang</span>
                <span class="font-black">{{ $prioritySummary['medium'] ?? 0 }}</span>
            </div>

            <div class="flex justify-between border-b border-white/10 pb-2">
                <span>Rendah</span>
                <span class="font-black">{{ $prioritySummary['low'] ?? 0 }}</span>
            </div>

            <div class="flex justify-between pt-1">
                <span class="font-semibold">Total</span>
                <span class="font-black">
                    Rp {{ number_format($prioritySummary['total'] ?? 0,0,',','.') }}
                </span>
            </div>

        </div>

        {{-- BUTTON --}}
        <a href="{{ route('admin.prioritas-tunggakan') }}"
           class="block w-full py-3 bg-white text-red-600 rounded-2xl text-center text-xs font-black hover:bg-red-50 transition-colors">
            Lihat Detail
        </a>

    </div>

</div>
    </div>
</div>

    {{-- 5. FINANCIAL CHARTS --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Performa Keuangan</h2>
                <p class="text-slate-500 text-sm">Laporan penerimaan iuran dalam 6 bulan terakhir.</p>
            </div>
            <div class="flex gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[var(--homi-blue)] opacity-50"></span>
                    <span class="text-xs font-bold text-slate-600">Penerimaan Lunas</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[var(--homi-orange)]"></span>
                    <span class="text-xs font-bold text-slate-600">Proyeksi Pending</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            <div class="lg:col-span-3">
                 <div class="h-[350px]">
                    <canvas id="chart-main" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="flex flex-col justify-center">
                <div class="h-48 mb-6">
                    <canvas id="chart-donut"></canvas>
                </div>
                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Lunas ({{ now()->format('Y') }})</p>
                        <p class="text-xl font-black text-slate-900">Rp {{ number_format(array_sum($chartMonthly['data'] ?? []), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rupiah = (n) => new Intl.NumberFormat('id-ID').format(Number(n || 0));

    // ==== DATA ====
    const labels = @json($chartMonthly['labels'] ?? []);
    const data   = @json($chartMonthly['data'] ?? []);
    
    const statusLabels = @json($chartStatus['labels'] ?? []);
    const statusData   = @json($chartStatus['data'] ?? []);

    // ==== GLOBAL SETUP ====
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = "#94a3b8";
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 12;

    // ==== MAIN CHART (Bar with gradient) ====
    const ctxMain = document.getElementById('chart-main').getContext('2d');

    new Chart(ctxMain, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Lunas',
                data: data,
                backgroundColor: 'rgba(31, 111, 139, 0.15)',
                borderColor: '#1f6f8b',
                borderWidth: 2,
                borderRadius: 12,
                hoverBackgroundColor: '#1f6f8b',
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` Rp ${rupiah(ctx.raw)}`
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { 
                    beginAtZero: true, 
                    border: { display: false, dash: [4, 4] },
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: (v) => 'Rp ' + rupiah(v)
                    }
                }
            }
        }
    });

    // ==== DONUT CHART ====
    new Chart(document.getElementById('chart-donut'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: Rp ${rupiah(ctx.raw)}`
                    }
                }
            }
        }
    });
});
</script>
@endpush
