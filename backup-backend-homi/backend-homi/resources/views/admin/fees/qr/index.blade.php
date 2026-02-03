@extends('layouts.app')

@section('title', 'QR Iuran')
@section('page_title', 'QR Iuran')
@section('page_subtitle', 'Kelola QR aktif untuk pembayaran iuran')

@php
    function qr_src($row) {
        $url = $row->qr_url ?? $row->url ?? null;
        if ($url) return $url;

        $path = $row->image_path ?? $row->qr_image_path ?? $row->qr_path ?? $row->path ?? null;
        if (!$path) return null;

        $p = str_replace('\\', '/', $path);
        $p = ltrim($p, '/');

        if (preg_match('/^https?:\/\//i', $p)) return $p;

        $p = preg_replace('#^public/#', '', $p);
        $p = preg_replace('#^storage/#', '', $p);

        return asset('storage/'.$p);
    }

    function qr_src_bust($row) {
        $src = qr_src($row);
        if (!$src) return null;
        $v = optional($row->updated_at)->timestamp ?? optional($row->created_at)->timestamp ?? time();
        return $src . (str_contains($src, '?') ? '&' : '?') . 'v=' . $v;
    }
@endphp

@section('content')

@if(session('success'))
    <div class="mb-3 p-3 bg-green-100 text-green-800 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-3 p-3 bg-red-100 text-red-800 rounded-lg text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- QR Aktif --}}
    <div class="homi-card">
        <div class="homi-title mb-1">QR Aktif</div>
        <div class="homi-subtitle mb-3">QR yang dipakai saat membuat tagihan iuran.</div>

        @php $srcActive = $active ? qr_src_bust($active) : null; @endphp

        @if($active && $srcActive)
            <div class="flex flex-col sm:flex-row items-start gap-4">
                <img src="{{ $srcActive }}"
                     alt="QR Aktif"
                     class="w-full sm:w-56 max-w-full border rounded-xl bg-white p-2">

                <div class="flex-1">
                    <div class="text-sm text-gray-700">
                        <div class="font-semibold">ID QR: {{ $active->id }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            Status: <span class="font-semibold text-emerald-600">Aktif</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.fees.invoices.create') }}"
                           class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg bg-[var(--homi-blue)] text-white text-sm font-semibold hover:opacity-95">
                            Buat Tagihan
                        </a>
                    </div>

                    <div class="mt-2 text-[12px] text-gray-500">
                        *QR aktif tidak bisa dihapus. Aktifkan QR lain dulu jika ingin menghapus yang lama.
                    </div>
                </div>
            </div>
        @else
            <div class="text-sm text-gray-700">
                Belum ada QR aktif atau QR belum terbaca.
            </div>
            <div class="mt-3">
                <a href="#form-qr"
                   class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg bg-[var(--homi-blue)] text-white text-sm font-semibold hover:opacity-95">
                    Upload QR Sekarang
                </a>
            </div>
        @endif
    </div>

    {{-- Form Upload QR --}}
    <div class="homi-card" id="form-qr">
        <div class="homi-title mb-1">Upload QR Baru</div>
        <div class="homi-subtitle mb-3">Upload gambar QR (png/jpg/webp). Setelah disimpan otomatis jadi aktif.</div>

        <form action="{{ route('admin.fees.qr.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf

            <div>
                <label class="text-sm font-semibold text-gray-700">Gambar QR</label>
                <input type="file" name="qr_image"
                       class="w-full border rounded-lg p-2 bg-white" required>
                <div class="text-xs text-gray-500 mt-1">Maks 5MB</div>
            </div>

            <button class="w-full sm:w-auto px-4 py-2 rounded-lg bg-[var(--homi-orange)] text-white text-sm font-semibold hover:bg-orange-500">
                Simpan & Jadikan Aktif
            </button>
        </form>
    </div>
</div>

{{-- Riwayat QR --}}
<div class="homi-card mt-4">
    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="homi-title">Riwayat QR</div>
            <div class="homi-subtitle">Daftar QR yang pernah diupload.</div>
        </div>
    </div>

    {{-- MOBILE: CARDS --}}
    <div class="mt-3 space-y-3 md:hidden">
        @forelse($items as $it)
            @php
                $src = qr_src_bust($it);
                $isActive = (int)($it->is_active ?? 0) === 1;
            @endphp

            <div class="rounded-xl border border-[var(--homi-border)] bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-xs text-gray-500">ID: {{ $it->id }}</div>
                        <div class="mt-1">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <div class="shrink-0">
                        @if($src)
                            <img src="{{ $src }}" class="w-20 border rounded-lg bg-white p-1" />
                        @endif
                    </div>
                </div>

                <div class="mt-3 text-[12px] text-gray-600">
                    Dibuat: {{ $it->created_at ?? '-' }}
                </div>

                <div class="mt-4 space-y-2">
                    @if(!$isActive)
                        <form action="{{ route('admin.fees.qr.activate', $it->id) }}" method="POST">
                            @csrf
                            <button class="w-full px-3 py-2 rounded-lg bg-gray-900 text-white text-xs hover:opacity-90">
                                Jadikan Aktif
                            </button>
                        </form>

                        {{-- ✅ Hapus QR nonaktif --}}
                        <form action="{{ route('admin.fees.qr.destroy', $it->id) }}"
                              method="POST"
                              onsubmit="return confirm('Yakin hapus QR ini? File gambarnya juga akan dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button class="w-full px-3 py-2 rounded-lg bg-rose-600 text-white text-xs hover:bg-rose-700">
                                Hapus
                            </button>
                        </form>
                    @else
                        <div class="text-xs text-gray-500">Sedang aktif (tidak bisa dihapus)</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-8 text-center text-gray-500">
                Belum ada data QR.
            </div>
        @endforelse
    </div>

    {{-- DESKTOP: TABLE --}}
    <div class="overflow-x-auto mt-3 hidden md:block">
        <table class="homi-table min-w-[760px] w-full">
            <thead>
                <tr>
                    <th class="text-left">ID</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Preview</th>
                    <th class="text-left">Dibuat</th>
                    <th class="text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $it)
                    @php
                        $src = qr_src_bust($it);
                        $isActive = (int)($it->is_active ?? 0) === 1;
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap">{{ $it->id }}</td>

                        <td class="whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap">
                            @if($src)
                                <img src="{{ $src }}" class="w-20 border rounded-lg bg-white p-1" />
                            @else
                                <span class="text-xs text-gray-500">-</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap text-sm text-gray-600">
                            {{ $it->created_at ?? '-' }}
                        </td>

                        <td class="whitespace-nowrap">
                            @if(!$isActive)
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.fees.qr.activate', $it->id) }}" method="POST">
                                        @csrf
                                        <button class="px-3 py-2 rounded-lg bg-gray-900 text-white text-xs hover:opacity-90">
                                            Jadikan Aktif
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.fees.qr.destroy', $it->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin hapus QR ini? File gambarnya juga akan dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-2 rounded-lg bg-rose-600 text-white text-xs hover:bg-rose-700">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-500">Sedang aktif</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-6">
                            Belum ada data QR.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>

@endsection
