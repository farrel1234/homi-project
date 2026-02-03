@extends('layouts.app')

@section('title','Notifikasi')

@section('content')
<div class="homi-card">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
        <div class="min-w-0">
            <div class="homi-title">Notifikasi</div>
            <div class="homi-subtitle">Riwayat notifikasi yang dikirim ke warga (in-app).</div>
        </div>

        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:items-center w-full lg:w-auto">
            <form method="GET" action="{{ route('admin.notifications.index') }}"
                  class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                <input type="text" name="q" value="{{ $q ?? '' }}"
                       placeholder="Cari warga / judul / isi..."
                       class="w-full sm:w-64 border border-[var(--homi-border)] rounded-full px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-sky-200">
                <button class="w-full sm:w-auto px-4 py-2 rounded-full bg-[var(--homi-blue)] text-white text-sm font-semibold hover:opacity-95">
                    Cari
                </button>
                @if($q)
                    <a href="{{ route('admin.notifications.index') }}"
                       class="text-xs text-gray-500 hover:underline text-center sm:text-left">
                        Reset
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.notifications.create') }}"
               class="w-full sm:w-auto text-center px-4 py-2 rounded-lg bg-[var(--homi-orange)] text-white text-sm font-semibold hover:bg-orange-500">
                + Kirim
            </a>
        </div>
    </div>

    @if(session('ok'))
        <div class="mt-4 p-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm border border-emerald-100">
            {{ session('ok') }}
        </div>
    @endif

    {{-- MOBILE: CARD LIST --}}
    <div class="mt-4 space-y-3 md:hidden">
        @forelse($items as $n)
            @php
                $u = $n->user;
                $name = $u->full_name ?? $u->name ?? $u->username ?? '-';
                $badge = $n->read_at ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800';
            @endphp

            <div class="rounded-xl border border-[var(--homi-border)] bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-semibold text-gray-900 break-words">{{ $name }}</div>
                        <div class="text-[11px] text-gray-500 break-words">{{ $u->email ?? '-' }}</div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $badge }}">
                        {{ $n->read_at ? 'Dibaca' : 'Belum dibaca' }}
                    </span>
                </div>

                <div class="mt-3">
                    <div class="font-semibold text-gray-900 text-sm break-words">{{ $n->title }}</div>
                    <div class="text-[12px] text-gray-600 mt-1 break-words">{{ $n->message }}</div>
                </div>

                <div class="mt-3 text-[12px] text-gray-500 flex flex-wrap gap-2">
                    <span>Tipe: <span class="text-gray-700">{{ $n->type }}</span></span>
                    <span>•</span>
                    <span>{{ optional($n->created_at)->format('d M Y H:i') }}</span>
                </div>
            </div>
        @empty
            <div class="py-10 text-center text-gray-500">
                Belum ada notifikasi.
            </div>
        @endforelse
    </div>

    {{-- DESKTOP: TABLE --}}
    <div class="mt-4 overflow-x-auto hidden md:block">
        <table class="homi-table min-w-[900px] w-full">
            <thead>
                <tr>
                    <th class="text-left">Warga</th>
                    <th class="text-left">Judul</th>
                    <th class="text-left">Tipe</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Dikirim</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $n)
                    @php
                        $u = $n->user;
                        $name = $u->full_name ?? $u->name ?? $u->username ?? '-';
                        $badge = $n->read_at ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800';
                    @endphp
                    <tr>
                        <td>
                            <div class="font-semibold text-gray-900 text-sm">{{ $name }}</div>
                            <div class="text-[11px] text-gray-500">{{ $u->email ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="font-semibold text-gray-900 text-sm">{{ $n->title }}</div>
                            <div class="text-[12px] text-gray-600 line-clamp-2">{{ $n->message }}</div>
                        </td>
                        <td class="text-sm text-gray-700">{{ $n->type }}</td>
                        <td>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $badge }}">
                                {{ $n->read_at ? 'Dibaca' : 'Belum dibaca' }}
                            </span>
                        </td>
                        <td class="text-sm text-gray-700 whitespace-nowrap">
                            {{ optional($n->created_at)->format('d M Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            Belum ada notifikasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->withQueryString()->links() }}
    </div>
</div>
@endsection
