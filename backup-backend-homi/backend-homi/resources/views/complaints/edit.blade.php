@extends('layouts.app')

@section('title','Detail Pengaduan')

@section('content')
@php
    $judul = $item->perihal ?? '-';

    $badgeClass = match($item->status) {
        'baru'      => 'bg-gray-100 text-gray-700',
        'diproses'  => 'bg-sky-100 text-sky-800',
        'selesai'   => 'bg-emerald-100 text-emerald-800',
        default     => 'bg-gray-100 text-gray-700',
    };

    $statusLabel = match($item->status) {
        'baru'      => 'Baru',
        'diproses'  => 'Diproses',
        'selesai'   => 'Selesai',
        default     => $item->status ?? '-',
    };

    $tglPengaduan = $item->tanggal_pengaduan
        ? \Illuminate\Support\Carbon::parse($item->tanggal_pengaduan)->format('d M Y')
        : '-';

    $dibuat = optional($item->created_at)->format('d M Y H:i') ?? '-';
    $selesai = $item->resolved_at
        ? \Illuminate\Support\Carbon::parse($item->resolved_at)->format('d M Y H:i')
        : null;

    $pelaporNama = $item->nama_pelapor
        ?? (optional($item->user)->full_name ?? optional($item->user)->username ?? '-');

    $pelaporEmail = optional($item->user)->email;
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-1">
        <div class="homi-title">Detail Pengaduan</div>
        <div class="homi-subtitle">
            Lihat detail laporan yang dikirim warga dan perbarui status penanganan.
        </div>
    </div>

    {{-- Flash --}}
    @if(session('ok'))
        <div class="p-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm border border-emerald-100">
            {{ session('ok') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 rounded-lg bg-rose-50 text-rose-700 text-sm border border-rose-100">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-3 rounded-lg bg-rose-50 text-rose-700 text-sm border border-rose-100">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-4">

        {{-- KIRI: Detail --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="homi-card space-y-4">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Laporan dari Warga
                </div>

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Judul Pengaduan</div>
                        <div class="text-base font-semibold text-gray-900">
                            {{ $judul }}
                        </div>
                    </div>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Tempat Kejadian</div>
                        <div class="text-gray-800">
                            {{ $item->tempat_kejadian ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 mb-1">Tanggal Pengaduan</div>
                        <div class="text-gray-800">
                            {{ $tglPengaduan }}
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Pelapor</div>
                        <div class="text-gray-800 font-medium">
                            {{ $pelaporNama }}
                        </div>
                        @if($pelaporEmail)
                            <div class="text-[11px] text-gray-500">
                                {{ $pelaporEmail }}
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 mb-1">Waktu</div>
                        <div class="text-[12px] text-gray-600">
                            Dibuat: <span class="font-medium text-gray-800">{{ $dibuat }}</span>
                        </div>
                        @if($selesai)
                            <div class="text-[12px] text-gray-600 mt-1">
                                Selesai: <span class="font-medium text-gray-800">{{ $selesai }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Foto bukti --}}
                @if($item->foto_url)
                    <div>
                        <div class="text-xs text-gray-500 mb-2">Foto Bukti</div>
                        <div class="border border-[var(--homi-border)] rounded-xl overflow-hidden bg-gray-50">
                            <img src="{{ $item->foto_url }}"
                                 alt="Foto bukti"
                                 class="w-full max-h-[420px] object-contain bg-white">
                        </div>
                        <a href="{{ $item->foto_url }}" target="_blank"
                           class="inline-flex items-center text-xs text-sky-700 hover:underline mt-2">
                            Buka foto di tab baru
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- KANAN: Tindakan Admin --}}
        <div class="space-y-4">
            <div class="homi-card space-y-4">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Tindakan Admin
                </div>

                <form method="POST"
                      action="{{ route('complaints.update', $item->id) }}"
                      class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ubah Status</label>
                        <select name="status"
                                class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-sky-200">
                            <option value="baru"     @selected($item->status=='baru')>Baru</option>
                            <option value="diproses" @selected($item->status=='diproses')>Diproses</option>
                            <option value="selesai"  @selected($item->status=='selesai')>Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ditugaskan ke</label>
                        <select name="assigned_to"
                                class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-sky-200">
                            <option value="">- Tidak ada -</option>
                            @foreach($admins as $a)
                                <option value="{{ $a->id }}" @selected($item->assigned_to == $a->id)>
                                    {{ $a->full_name ?? $a->username ?? ('Admin #'.$a->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-[var(--homi-orange)] text-white text-sm font-semibold hover:bg-orange-500">
                            Simpan
                        </button>

                        <a href="{{ route('complaints.index') }}"
                           class="px-4 py-2 rounded-lg border border-[var(--homi-border)] text-sm text-gray-700 hover:bg-gray-50">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
