@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
<div class="homi-card">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div class="min-w-0">
            <div class="homi-title">Pengumuman</div>
            <div class="homi-subtitle">
                Kelola pengumuman yang akan ditampilkan kepada warga di aplikasi HOMI.
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:items-center w-full lg:w-auto">
            {{-- Search --}}
            <form action="{{ route('announcements.index') }}" method="GET"
                  class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                <input type="text"
                       name="q"
                       value="{{ $q ?? request('q') }}"
                       placeholder="Cari judul/kategori"
                       class="border border-[var(--homi-border)] rounded-full px-3 py-2 text-sm w-full sm:w-64
                              focus:outline-none focus:ring-2 focus:ring-sky-200" />
                <button type="submit"
                        class="px-4 py-2 rounded-full bg-[var(--homi-blue)] text-white text-sm font-semibold hover:opacity-95
                               w-full sm:w-auto">
                    Cari
                </button>
            </form>

            {{-- Button tambah --}}
            <a href="{{ route('announcements.create') }}"
               class="px-4 py-2 rounded-lg bg-[var(--homi-orange)] text-white text-sm font-semibold hover:bg-orange-500
                      text-center w-full sm:w-auto">
                + Tambah
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('ok'))
        <div class="mt-4 p-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm">
            {{ session('ok') }}
        </div>
    @endif

    {{-- ===== MOBILE (Card List) ===== --}}
    <div class="mt-4 space-y-3 md:hidden">
        @forelse($announcements as $item)
            <div class="rounded-xl border border-[var(--homi-border)] bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-semibold text-gray-900 truncate">
                            {{ $item->title }}
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-100">
                                {{ $item->category ?? '-' }}
                            </span>
                            <span>
                                {{ $item->created_at?->format('d M Y') ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex flex-col sm:flex-row gap-2">
                    <a href="{{ route('announcements.edit', $item) }}"
                       class="px-3 py-2 rounded-lg text-xs font-semibold border border-sky-200 text-sky-700 hover:bg-sky-50
                              text-center w-full sm:w-auto">
                        Edit
                    </a>

                    <form action="{{ route('announcements.destroy', $item) }}"
                          method="POST"
                          class="w-full sm:w-auto"
                          onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-2 rounded-lg text-xs font-semibold border border-rose-200 text-rose-700 hover:bg-rose-50
                                       text-center w-full sm:w-auto">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-10 text-center text-gray-500">
                Belum ada pengumuman.
            </div>
        @endforelse
    </div>

    {{-- ===== DESKTOP/TABLET (Table) ===== --}}
    <div class="mt-4 overflow-x-auto hidden md:block">
        <table class="homi-table min-w-[720px] w-full">
            <thead>
                <tr>
                    <th class="text-left">Judul</th>
                    <th class="text-left">Kategori</th>
                    <th class="text-left">Tanggal</th>
                    <th class="text-center w-44">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $item)
                    <tr>
                        <td class="font-medium text-gray-800">
                            <div class="max-w-[520px] truncate">
                                {{ $item->title }}
                            </div>
                        </td>
                        <td>
                            {{ $item->category ?? '-' }}
                        </td>
                        <td>
                            {{ $item->created_at?->format('d M Y') ?? '-' }}
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('announcements.edit', $item) }}"
                                   class="px-3 py-2 rounded-lg text-xs font-semibold border border-sky-200 text-sky-700 hover:bg-sky-50">
                                    Edit
                                </a>

                                <form action="{{ route('announcements.destroy', $item) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-2 rounded-lg text-xs font-semibold border border-rose-200 text-rose-700 hover:bg-rose-50">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-gray-500">
                            Belum ada pengumuman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(method_exists($announcements, 'links'))
        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
    @endif
</div>
@endsection
