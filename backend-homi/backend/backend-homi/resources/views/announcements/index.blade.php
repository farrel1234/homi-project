@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER HALAMAN --}}
    <div class="mb-4">
        <h1 class="text-2xl font-semibold text-gray-800">Pengumuman</h1>
        <p class="text-sm text-gray-500 mt-1">
            Kelola pengumuman yang akan ditampilkan kepada warga di aplikasi HOMI.
        </p>
    </div>

    <div class="bg-white rounded-2xl shadow p-5">

        {{-- BARIS ATAS: JUDUL BOX + BUTTON TAMBAH + SEARCH --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Daftar Pengumuman</h2>
                <p class="text-xs text-gray-500">
                    Cari pengumuman berdasarkan judul atau kategori.
                </p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Search --}}
                <form action="{{ route('announcements.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ $q ?? '' }}"
                           placeholder="Cari judul/kategori"
                           class="border rounded-full px-3 py-1.5 text-xs w-52 focus:outline-none focus:ring-1 focus:ring-[var(--homi-blue)]" />
                    <button type="submit"
                            class="px-3 py-1.5 bg-[var(--homi-blue)] text-white text-xs rounded-full">
                        Cari
                    </button>
                </form>

                {{-- Tombol Tambah --}}
                <a href="{{ route('announcements.create') }}"
                   class="px-4 py-2 bg-[var(--homi-blue)] text-white text-xs font-semibold rounded-lg shadow hover:bg-sky-700">
                    + Tambah Pengumuman
                </a>
            </div>
        </div>

        {{-- FLASH MESSAGE --}}
        @if(session('ok'))
            <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 text-xs rounded-lg">
                {{ session('ok') }}
            </div>
        @endif

        {{-- TABEL PENGUMUMAN --}}
        <div class="border border-[#d3e1e8] rounded-2xl overflow-hidden">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-[#f6fbfd] text-gray-700">
                        <th class="px-6 py-3 text-left font-semibold border-b border-[#d3e1e8]">Judul</th>
                        <th class="px-6 py-3 text-left font-semibold border-b border-[#d3e1e8]">Kategori</th>
                        <th class="px-6 py-3 text-left font-semibold border-b border-[#d3e1e8]">Tanggal</th>
                        <th class="px-6 py-3 text-center font-semibold border-b border-[#d3e1e8] w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $item)
                        <tr class="hover:bg-[#f9fcff]">
                            <td class="px-6 py-3 border-t border-[#d3e1e8]">
                                {{ $item->title }}
                            </td>
                            <td class="px-6 py-3 border-t border-[#d3e1e8]">
                                {{ $item->category ?? '-' }}
                            </td>
                            <td class="px-6 py-3 border-t border-[#d3e1e8]">
                                {{ $item->created_at?->format('d M Y') }}
                            </td>
                            <td class="px-6 py-3 border-t border-[#d3e1e8]">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('announcements.edit', $item) }}"
                                       class="px-3 py-1 border border-[#6bb3d6] text-[#2f79a0] rounded-lg text-xs hover:bg-[#e7f5fb]">
                                        Edit
                                    </a>

                                    <form action="{{ route('announcements.destroy', $item) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 border border-[#ff9fa5] text-[#e0525e] rounded-lg text-xs hover:bg-[#ffecee]">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="px-6 py-6 text-center text-gray-500 text-xs">
                                Belum ada pengumuman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION (kalau pakai paginate) --}}
        @if(method_exists($announcements, 'links'))
            <div class="mt-4">
                {{ $announcements->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
