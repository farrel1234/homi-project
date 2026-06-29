@extends('layouts.app')

@section('title', 'Detail Pembayaran')
@section('page_title', 'Detail Pembayaran')
@section('page_subtitle', 'Informasi transaksi, bukti, dan approval')

@section('content')
@php
    $rs = $payment->review_status ?? 'pending';
    $label = match($rs) {
        'pending'  => 'Menunggu Review',
        'approved' => 'Pembayaran Sah',
        'rejected' => 'Pembayaran Ditolak',
        default    => ucfirst($rs)
    };
    $trxId = optional($payment->invoice)->trx_id ?? '-';
@endphp

<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('payments.index') }}" 
               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="homi-title">Detail Pembayaran</h1>
                <p class="homi-subtitle">Review bukti transfer dan konfirmasi iuran warga</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="homi-card bg-emerald-50 border-emerald-200 text-sm text-emerald-800 flex items-center gap-3">
            <svg viewBox="0 0 24 24" class="h-5 w-5 text-emerald-500 fill-none stroke-current stroke-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            {{-- Info Transaksi --}}
            <div class="homi-card">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-1 bg-[var(--homi-blue)] rounded-full"></div>
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Informasi Transaksi</h2>
                    </div>
                    <span class="homi-badge {{ 
                        match($rs) {
                            'pending' => 'homi-badge-pending',
                            'approved' => 'homi-badge-success',
                            'rejected' => 'homi-badge-danger',
                            default => 'homi-badge-info'
                        }
                    }}">
                        {{ $label }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-4">
                    <dl class="homi-dl">
                        <dt>ID Pembayaran / TRX</dt>
                        <dd>
                            <span class="text-gray-900 font-bold">#{{ $payment->id }}</span>
                            <span class="mx-1 text-gray-300">/</span>
                            <span class="font-mono text-xs text-gray-500">{{ $trxId }}</span>
                        </dd>
                    </dl>

                    <dl class="homi-dl">
                        <dt>Iuran & Periode</dt>
                        <dd class="text-gray-900 font-semibold">{{ $feeName }} <span class="text-gray-400 font-normal">({{ $periodText }})</span></dd>
                    </dl>

                    <dl class="homi-dl">
                        <dt>Jumlah Nominal</dt>
                        <dd class="text-xl font-extrabold text-[var(--homi-blue)]">
                            {{ !is_null($amount) ? 'Rp ' . number_format($amount, 0, ',', '.') : '-' }}
                        </dd>
                    </dl>

                    <dl class="homi-dl">
                        <dt>Batas Jatuh Tempo</dt>
                        <dd class="text-rose-600 font-bold">{{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d M Y') : '-' }}</dd>
                    </dl>

                    <dl class="homi-dl">
                        <dt>Waktu Bayar (Upload)</dt>
                        <dd class="text-gray-700">{{ $payment->created_at ? $payment->created_at->format('d M Y, H:i') : '-' }}</dd>
                    </dl>

                    <dl class="homi-dl">
                        <dt>Status Review</dt>
                        <dd class="text-gray-700">
                             {{ $payment->reviewed_at ? 'Diproses pada ' . $payment->reviewed_at->format('d/m/Y H:i') : 'Menunggu antrean' }}
                        </dd>
                    </dl>
                </div>
            </div>


            {{-- BUKTI PEMBAYARAN --}}
            @php
                $proof = $payment->proof_path ?? null;
                $proofUrl = null;

                if ($proof) {
                    if (preg_match('/^https?:\\/\\//i', $proof)) {
                        $proofUrl = $proof;
                    } else {
                        $proofUrl = asset('storage/' . ltrim($proof, '/'));
                    }
                }
            @endphp

            <div class="homi-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-1 bg-[var(--homi-orange)] rounded-full"></div>
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Bukti Transfer</h2>
                    </div>
                    
                    @if($proofUrl)
                        <button id="btn-scan-ocr" class="homi-btn bg-sky-50 text-sky-700 border border-sky-100 py-1.5 text-[11px]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 fill-none stroke-current stroke-2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            Validasi OCR
                        </button>
                    @endif
                </div>

                @if($proofUrl)
                    <div class="space-y-4">
                        <div class="relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 flex items-center justify-center p-2 min-h-[300px]">
                            <img src="{{ $proofUrl }}" alt="Bukti pembayaran" class="max-h-[500px] w-auto rounded-lg shadow-lg">
                            
                            {{-- Overlay Loading --}}
                            <div id="ocr-loading" class="absolute inset-0 bg-white/80 backdrop-blur-md hidden items-center justify-center z-10 transition-all">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="h-10 w-10 border-4 border-sky-200 border-t-sky-600 rounded-full animate-spin"></div>
                                    <span class="text-xs font-bold text-sky-900 tracking-widest uppercase">Menganalisis Bukti...</span>
                                </div>
                            </div>
                        </div>

                        {{-- Hasil OCR --}}
                        <div id="ocr-result" class="hidden rounded-2xl border border-sky-100 bg-sky-50/50 p-5 animate-in zoom-in-95 duration-300">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm text-sky-600 border border-sky-100">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-slate-800">Hasil Analisis AI (OCR)</h4>
                                    
                                    <div class="mt-4 grid grid-cols-2 gap-4">
                                        <div class="bg-white p-3 rounded-xl border border-sky-100">
                                            <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Nominal Terbaca</p>
                                            <p id="ocr-amount" class="text-xl font-black text-sky-700">-</p>
                                        </div>
                                        <div class="bg-white p-3 rounded-xl border border-sky-100">
                                            <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Status Validasi</p>
                                            <div id="ocr-status-badge" class="mt-1"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="button" onclick="document.getElementById('ocr-raw-container').classList.toggle('hidden')" 
                                                class="text-[11px] font-bold text-sky-600 hover:text-sky-800 flex items-center gap-1">
                                            <span>Lihat Data Mentah</span>
                                            <svg viewBox="0 0 24 24" class="h-3 w-3 fill-none stroke-current stroke-2"><path d="M6 9l6 6 6-6"/></svg>
                                        </button>
                                        <div id="ocr-raw-container" class="hidden mt-2 p-3 bg-slate-900 rounded-xl text-[10px] font-mono text-emerald-400 overflow-y-auto max-h-32 border border-slate-800">
                                            <pre id="ocr-raw-text" class="whitespace-pre-wrap opacity-80"></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <a href="{{ $proofUrl }}" target="_blank" class="homi-btn homi-btn-secondary w-full">
                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/></svg>
                                Buka Gambar Penuh
                            </a>
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <svg viewBox="0 0 24 24" class="h-12 w-12 mx-auto text-slate-300 mb-2 opacity-50"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7M16 5l-4.5 4.5L10 8"/><circle cx="18.5" cy="5.5" r="2.5"/></svg>
                        <p class="text-sm font-medium text-slate-400">Bukti transfer belum diunggah.</p>
                    </div>
                @endif
            </div>

            @if($payment->note)
            <div class="homi-card bg-amber-50/50 border-amber-100">
                <div class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-2">Catatan dari Warga</div>
                <p class="text-xs text-amber-800 leading-relaxed italic">"{{ $payment->note }}"</p>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Data Warga --}}
            <div class="homi-card bg-slate-50/50 shadow-none border-slate-100">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Profil Pembayar</div>
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-full bg-white border border-slate-200 flex items-center justify-center text-lg font-bold text-[var(--homi-blue)] shadow-sm">
                        {{ strtoupper(substr($name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-gray-900 truncate">{{ $name }}</div>
                        <div class="text-[11px] text-gray-500 truncate">{{ $payer->email ?? '-' }}</div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center gap-3 text-xs">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 text-slate-400 fill-none stroke-current stroke-2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span class="text-slate-600 font-medium">{{ $payer->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Formulir Approval --}}
            <div class="homi-card border-t-4 border-t-[var(--homi-blue)]">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">Tindakan Admin</h3>

                <form method="POST" action="{{ route('payments.approve', $payment) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="homi-label">Catatan / Pesan Konfirmasi</label>
                        <textarea name="reason" rows="2" class="homi-input" placeholder="Bukti transfer sudah sesuai..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 gap-2">
                        @if($rs !== 'approved')
                            <button type="submit" class="homi-btn homi-btn-success w-full">
                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M20 6L9 17l-5-5"/></svg>
                                Terima Pembayaran
                            </button>
                        @endif

                        @if($rs !== 'rejected')
                            <button type="submit" formaction="{{ route('payments.reject', $payment) }}" class="homi-btn homi-btn-danger w-full">
                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                Tolak Pembayaran
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

