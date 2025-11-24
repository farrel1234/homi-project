@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Daftar Pengumuman</h1>
            <p class="text-sm text-gray-500">Kelola pengumuman untuk warga Hawai Garden.</p>
        </div>

        <a href="{{ route('announcements.create') }}"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700">
            + Tambah Pengumuman
        </a>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter / Search --}}
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text"
               name="q"
               value="{{ $q }}"
               placeholder="Cari judul pengumuman..."
               class="w-full md:w-64 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-sky-500 focus:border-sky-500">
        <button class="px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white hover:bg-gray-50">
            Cari
        </button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Publik?</th>
                    <th class="px-4 py-3">Periode</th>
                    <th class="px-4 py-3">Dibuat</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $item)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/60">
                        <td class="px-4 py-3">
                            <a href="{{ route('announcements.show', $item) }}"
                               class="font-medium text-gray-800 hover:text-sky-600">
                                {{ $item->title }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            @if($item->is_public)
                                <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Publik
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-gray-50 text-gray-600 border border-gray-200">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            @if($item->start_at || $item->end_at)
                                {{ optional($item->start_at)->format('d M Y') ?? '-' }}
                                —
                                {{ optional($item->end_at)->format('d M Y') ?? '-' }}
                            @else
                                <span class="text-gray-400">Tidak ditentukan</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $item->created_at?->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('announcements.edit', $item) }}"
                                   class="text-xs px-2 py-1 rounded border border-sky-500 text-sky-600 hover:bg-sky-50">
                                    Edit
                                </a>
                                <form action="{{ route('announcements.destroy', $item) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin hapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs px-2 py-1 rounded border border-red-500 text-red-600 hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                            Belum ada pengumuman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($announcements->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
