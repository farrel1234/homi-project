@extends('layouts.app')

@section('title','Detail Pengaduan')

@section('content')
@php
    $judul = $item->perihal ?? '-';

    $badgeClass = match($item->status) {
        'baru'      => 'bg-gray-100 text-gray-700 border border-gray-200',
        'diproses'  => 'bg-sky-50 text-sky-700 border border-sky-200',
        'selesai'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        default     => 'bg-gray-50 text-gray-600 border border-gray-200',
    };

    $statusLabel = match($item->status) {
        'baru'      => 'Baru',
        'diproses'  => 'Diproses',
        'selesai'   => 'Selesai',
        default     => $item->status ?? '-',
    };
@endphp

<div class="space-y-6">

    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Detail Pengaduan</h1>
        <p class="homi-subtitle">
            Lihat detail laporan yang dikirim warga dan perbarui status penanganan.
        </p>
    </div>

    @if(session('ok'))
        <div class="homi-card bg-emerald-50 border-emerald-200 text-sm text-emerald-800">
            {{ session('ok') }}
        </div>
    @endif
    @if(session('error'))
        <div class="homi-card bg-rose-50 border-rose-200 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="homi-card bg-rose-50 border-rose-200 text-sm text-rose-800">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-4">

        {{-- KOLOM KIRI --}}
        <div class="md:col-span-2 space-y-4">

            <div class="homi-card space-y-4">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Laporan dari Warga
                </div>

                <div>
                    <div class="text-xs text-gray-500 mb-1">Judul Pengaduan</div>
                    <div class="text-sm font-semibold text-gray-900">
                        {{ $judul }}
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Tempat Kejadian</div>
                        <div class="text-gray-800">
                            {{ $item->tempat_kejadian ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Tanggal Pengaduan</div>
                        <div class="text-gray-800">
                            {{ $item->tanggal_pengaduan ? \Illuminate\Support\Carbon::parse($item->tanggal_pengaduan)->format('d M Y') : '-' }}
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Pelapor</div>
                        <div class="text-gray-800">
                            {{ $item->nama_pelapor ?? ($item->user->full_name ?? $item->user->username ?? '-') }}
                        </div>
                        @if(optional($item->user)->email)
                            <div class="text-[11px] text-gray-500">
                                {{ $item->user->email }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Status</div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                {{-- Foto bukti --}}
                @if($item->foto_url)
                    <div>
                        <div class="text-xs text-gray-500 mb-2">Foto Bukti</div>
                        <div class="border rounded-xl overflow-hidden bg-gray-50">
                            <img src="{{ $item->foto_url }}" alt="Foto bukti"
                                 class="w-full max-h-[420px] object-contain">
                        </div>
                        <a href="{{ $item->foto_url }}" target="_blank"
                           class="inline-flex items-center text-xs text-sky-700 hover:underline mt-2">
                            Buka foto di tab baru
                        </a>
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-3 text-[11px] text-gray-500">
                    <div>Dibuat: {{ optional($item->created_at)->format('d M Y H:i') ?? '-' }}</div>
                    @if($item->resolved_at)
                        <div>Selesai: {{ \Illuminate\Support\Carbon::parse($item->resolved_at)->format('d M Y H:i') }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN --}}
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
                        <label class="block text-sm font-medium mb-1">Ubah Status</label>
                        <select name="status"
                                class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                            <option value="baru"     @selected($item->status=='baru')>Baru</option>
                            <option value="diproses" @selected($item->status=='diproses')>Diproses</option>
                            <option value="selesai"  @selected($item->status=='selesai')>Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Ditugaskan ke</label>
                        <select name="assigned_to"
                                class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
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
                            class="px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white text-sm font-semibold hover:bg-sky-800">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('complaints.index') }}"
                           class="px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-600 hover:bg-gray-50">
                            Kembali ke daftar
                        </a>
                    </div>
                </form>

            </div>

        </div>
    </div>

</div>
@endsection
