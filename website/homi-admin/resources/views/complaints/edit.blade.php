@extends('layouts.app')

@section('title','Detail Pengaduan')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Detail Pengaduan</h1>
        <p class="homi-subtitle">
            Lihat detail laporan yang dikirim warga dan perbarui status penanganan.
        </p>
    </div>

    {{-- NOTIFIKASI --}}
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

    <div class="grid md:grid-cols-3 gap-4">

        {{-- KOLOM KIRI: LAPORAN WARGA --}}
        <div class="md:col-span-2 space-y-4">

            {{-- Laporan dari warga (readonly) --}}
            <div class="homi-card space-y-4">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Laporan dari Warga
                </div>

                {{-- Judul --}}
                <div>
                    <div class="text-xs text-gray-500 mb-1">Judul Pengaduan</div>
                    <div class="text-sm font-semibold text-gray-900">
                        {{ $item->title }}
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <div class="text-xs text-gray-500 mb-1">Deskripsi</div>
                    <p class="text-sm text-gray-800 whitespace-pre-line">
                        {{ $item->description }}
                    </p>
                </div>

                {{-- Info Pelapor & Kategori --}}
                <div class="grid md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Pelapor</div>
                        <div class="text-gray-800">
                            {{ $item->user->full_name ?? $item->user->username ?? '-' }}
                        </div>
                        @if($item->user->email)
                            <div class="text-[11px] text-gray-500">
                                {{ $item->user->email }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Kategori</div>
                        <div class="text-gray-800">
                            {{ $item->category ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Waktu --}}
                <div class="grid md:grid-cols-2 gap-3 text-[11px] text-gray-500">
                    <div>
                        Dibuat: {{ $item->created_at->format('d M Y H:i') }}
                    </div>
                    @if($item->resolved_at)
                        <div>
                            Selesai: {{ $item->resolved_at->format('d M Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kalau nanti mau tambah tabel log / riwayat, pakai homi-table --}}
            {{-- Contoh (kosong dulu, bisa diisi kalau sudah ada data log) --}}
            {{--
            <div class="homi-card space-y-3">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Riwayat Penanganan
                </div>

                <div class="overflow-x-auto">
                    <table class="homi-table text-xs text-left text-gray-800">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Oleh</th>
                                <th>Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>—</td>
                                <td>—</td>
                                <td>Belum ada riwayat.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            --}}
        </div>

        {{-- KOLOM KANAN: TINDAKAN ADMIN --}}
        <div class="space-y-4">

            <div class="homi-card space-y-4">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">
                    Tindakan Admin
                </div>

                {{-- STATUS SAAT INI --}}
                <div>
                    <div class="text-xs text-gray-500 mb-1">Status Saat Ini</div>
                    @php
                        $badgeClass = match($item->status) {
                            'submitted'     => 'bg-gray-100 text-gray-700 border border-gray-200',
                            'investigating' => 'bg-sky-50 text-sky-700 border border-sky-200',
                            'resolved'      => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                            'dismissed'     => 'bg-rose-50 text-rose-700 border border-rose-200',
                            default         => 'bg-gray-50 text-gray-600 border border-gray-200',
                        };
                        $statusLabel = match($item->status) {
                            'submitted'     => 'Diajukan',
                            'investigating' => 'Diselidiki',
                            'resolved'      => 'Selesai',
                            'dismissed'     => 'Ditolak',
                            default         => '-',
                        };
                    @endphp

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- FORM UPDATE --}}
                <form method="POST"
                      action="{{ route('complaints.update', $item->id) }}"
                      class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Ubah Status --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Ubah Status</label>
                        <select name="status"
                                class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                            <option value="submitted"     @selected($item->status=='submitted')>Diajukan</option>
                            <option value="investigating" @selected($item->status=='investigating')>Diselidiki</option>
                            <option value="resolved"      @selected($item->status=='resolved')>Selesai</option>
                            <option value="dismissed"     @selected($item->status=='dismissed')>Ditolak</option>
                        </select>
                    </div>

                    {{-- Ditugaskan ke --}}
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

                    {{-- Tombol --}}
                    <div class="flex flex-wrap gap-2 pt-1">
                        <button
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
