@extends('layouts.app')

@section('title', 'Detail Pengajuan')
@section('page_title', 'Detail Pengajuan')
@section('page_subtitle', 'Tinjau data pengajuan dan kelola status layanan')

@section('content')
<div class="space-y-6">
    {{-- Top Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('service-requests.index') }}" 
               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="homi-title">ID #{{ $item->id }}</h1>
                <p class="homi-subtitle">{{ $item->type->name ?? 'Layanan Umum' }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            @if($item->pdf_path || $item->status === 'approved')
                <a href="{{ route('service-requests.download', $item->id) }}"
                   class="homi-btn homi-btn-primary">
                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    Download PDF
                </a>
            @endif
        </div>
    </div>

    @if(session('ok'))
        <div class="homi-card bg-emerald-50 border-emerald-200 text-sm text-emerald-800 flex items-center gap-3">
            <svg viewBox="0 0 24 24" class="h-5 w-5 text-emerald-500 fill-none stroke-current stroke-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3"/></svg>
            {{ session('ok') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- LEFT COLUMN: Main Info & Data --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Summary Card --}}
            <div class="homi-card">
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-8 w-1 bg-[var(--homi-blue)] rounded-full"></div>
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Informasi Pengajuan</h2>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                    <dl class="homi-dl">
                        <dt>Pemohon (Warga)</dt>
                        <dd class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-[var(--homi-blue)]">
                                {{ strtoupper(substr($item->user?->name ?? 'W', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">{{ $item->user?->full_name ?? $item->user?->name ?? 'Warga' }}</div>
                                <div class="text-[11px] text-gray-500">{{ $item->user?->email ?? '-' }}</div>
                            </div>
                        </dd>
                    </dl>

                    <dl class="homi-dl">
                        <dt>Subjek / Perihal</dt>
                        <dd class="text-gray-900">{{ $item->subject ?? $item->title ?? '-' }}</dd>
                    </dl>

                    <dl class="homi-dl">
                        <dt>Tanggal & Lokasi Kejadian</dt>
                        <dd>
                            <span class="text-gray-900">{{ $item->request_date ? $item->request_date->format('d M Y') : '-' }}</span>
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="text-gray-600 font-normal italic">{{ $item->place ?? '-' }}</span>
                        </dd>
                    </dl>

                    <dl class="homi-dl">
                        <dt>Kategori</dt>
                        <dd>
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] uppercase font-bold">
                                {{ $item->category ?? 'Umum' }}
                            </span>
                        </dd>
                    </dl>

                    @if($item->description)
                    <dl class="homi-dl sm:col-span-2">
                        <dt>Keterangan / Deskripsi</dt>
                        <dd class="text-gray-700 leading-relaxed font-normal bg-slate-50 p-3 rounded-lg border border-slate-100">
                            {{ $item->description }}
                        </dd>
                    </dl>
                    @endif
                </div>
            </div>

            {{-- Data Input Card (Sleek List) --}}
            <div class="homi-card">
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-8 w-1 bg-[var(--homi-orange)] rounded-full"></div>
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Spesifikasi Data (Data Input)</h2>
                </div>

                @if(isset($dataInput) && count($dataInput))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($dataInput as $k => $v)
                            <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                                <span class="text-[11px] font-bold text-gray-500 uppercase">{{ str_replace('_', ' ', $k) }}</span>
                                <span class="text-sm font-semibold text-gray-900">{{ is_array($v) ? json_encode($v) : $v }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <svg viewBox="0 0 24 24" class="h-10 w-10 mx-auto text-slate-300 fill-none stroke-current stroke-1 mb-2"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/></svg>
                        <p class="text-xs text-slate-400">Tidak ada data spesifik yang dilampirkan.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: Actions & Management --}}
        <div class="space-y-6">
            
            {{-- Status & Quick Action --}}
            <div class="homi-card border-l-4 border-l-[var(--homi-blue)]">
                <div class="text-xs font-bold text-gray-400 uppercase mb-4 tracking-widest">Manajemen Status</div>
                
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <div class="text-[10px] text-gray-500 mb-1">Status Saat Ini</div>
                        <span class="homi-badge {{ 
                            match($item->status) {
                                'submitted' => 'homi-badge-pending',
                                'approved' => 'homi-badge-success',
                                'rejected' => 'homi-badge-danger',
                                default => 'homi-badge-info'
                            }
                        }}">
                            {{ $item->status_label ?? $item->status }}
                        </span>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-gray-500 mb-1">Dibuat Pada</div>
                        <div class="text-xs font-bold text-gray-800">{{ $item->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                        <form method="POST" action="{{ route('service-requests.approve', $item->id) }}" class="space-y-4">
                            @csrf
                            
                            {{-- Info Surat --}}
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-3">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Informasi Surat</div>
                                
                                @if(empty($item->subject))
                                <div>
                                    <label class="homi-label">Perihal / Subjek Surat</label>
                                    <input type="text" name="subject" class="homi-input" placeholder="Contoh: Pengurusan Adm. Kependudukan" value="{{ old('subject') }}">
                                </div>
                                @endif

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="homi-label">RT</label>
                                        <input type="text" name="rt" class="homi-input" placeholder="01" value="{{ old('rt', $item->user->residentProfile->rt ?? '') }}">
                                    </div>
                                    <div>
                                        <label class="homi-label">RW</label>
                                        <input type="text" name="rw" class="homi-input" placeholder="01" value="{{ old('rw', $item->user->residentProfile->rw ?? '') }}">
                                    </div>
                                </div>

                                <div>
                                    <label class="homi-label">Nama Ketua RT (Tanda Tangan)</label>
                                    <input type="text" name="nama_rt" class="homi-input" placeholder="Nama Lengkap Ketua RT" value="{{ old('nama_rt') }}">
                                </div>
                            </div>


                            <div>
                                <label class="homi-label">Catatan Admin</label>
                                <textarea name="admin_note" rows="2" class="homi-input" placeholder="Tulis catatan atau alasan di sini...">{{ old('admin_note', $item->admin_note) }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 gap-2">
                                <button type="submit" class="homi-btn homi-btn-success w-full shadow-lg">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M20 6L9 17l-5-5"/></svg>
                                    Approve & Terbitkan PDF
                                </button>
                            </div>
                        </form>
            </div>

            {{-- PDF Preview Card --}}
            <div class="relative overflow-hidden rounded-2xl bg-slate-900 p-6 shadow-xl group border border-slate-800">
                <div class="absolute -top-10 -right-10 p-8 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-700">
                    <svg viewBox="0 0 24 24" class="h-40 w-40 text-white fill-none stroke-current stroke-1"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/></svg>
                </div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-12 w-12 rounded-xl bg-rose-500/20 flex items-center justify-center border border-rose-500/30">
                            <svg viewBox="0 0 24 24" class="h-6 w-6 text-rose-400 fill-none stroke-current stroke-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8M16 17H8M12 9H8"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest leading-none mb-1">Dokumen Siap</p>
                            <h3 class="text-sm font-bold text-white leading-none">Draft Surat PDF</h3>
                        </div>
                    </div>
                    
                    <p class="text-[11px] text-slate-400 leading-relaxed mb-6">
                        System-generated draft berdasarkan data pengajuan. Klik tombol di bawah untuk meninjau secara mendalam.
                    </p>
                    
                    <a href="{{ route('service-requests.preview', $item->id) }}"
                       target="_blank"
                       class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-white text-slate-900 text-xs font-black uppercase tracking-widest hover:bg-slate-100 transition-all shadow-lg active:scale-95">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Buka Preview PDF
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

