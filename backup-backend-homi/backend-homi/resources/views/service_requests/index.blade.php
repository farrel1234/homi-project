@extends('layouts.app')

@section('title','Pengajuan Layanan / Surat')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Pengajuan Warga</h1>
        <p class="homi-subtitle">
            Kelola pengajuan warga (surat / layanan). Jika jenis pengajuan terhubung ke template surat, admin bisa mengunduh PDF.
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

    <div class="homi-card space-y-4">

        {{-- FILTER BAR --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-sm font-semibold text-gray-800">Daftar Pengajuan</h2>
                <p class="text-[12px] text-gray-500">Filter status / cari nama warga / jenis pengajuan.</p>
            </div>

            <form method="GET"
                  action="{{ route('service-requests.index') }}"
                  class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:items-center text-sm w-full lg:w-auto">

                <select name="status"
                        class="w-full sm:w-48 rounded-xl border-gray-300 px-3 py-2
                               focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                    <option value="">Semua status</option>
                    <option value="submitted"  @selected($status=='submitted')>Diajukan</option>
                    <option value="processed"  @selected($status=='processed')>Diproses</option>
                    <option value="approved"   @selected($status=='approved')>Disetujui</option>
                    <option value="rejected"   @selected($status=='rejected')>Ditolak</option>
                </select>

                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       placeholder="Cari nama warga / jenis pengajuan..."
                       class="w-full sm:w-72 rounded-xl border-gray-300 px-3 py-2
                              focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">

                <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white hover:opacity-95 font-medium">
                    Cari
                </button>

                @if($status || $q)
                    <a href="{{ route('service-requests.index') }}"
                       class="text-xs text-gray-500 hover:underline text-center sm:text-left">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- ===== MOBILE: CARD LIST ===== --}}
        <div class="space-y-3 md:hidden">
            @forelse($items as $req)
                @php
                    $nama = $req->user->full_name ?? $req->user->name ?? $req->user->username ?? '-';
                    $email = $req->user->email ?? '-';
                    $jenis = $req->type->name ?? '-';
                    $judul = $req->subject ?? $req->title ?? '-';
                    $tanggal = optional($req->created_at)->format('d M Y H:i') ?? '-';
                    $badgeClass = $req->status_badge_class ?? 'bg-gray-50 text-gray-600 border border-gray-200';
                    $statusLabel = $req->status_label ?? $req->status ?? '-';
                @endphp

                <div class="rounded-xl border border-[var(--homi-border)] bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900 break-words">
                                {{ $nama }}
                            </div>
                            <div class="text-[11px] text-gray-500 break-words">
                                {{ $email }}
                            </div>
                        </div>

                        <div class="shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-1 gap-2 text-sm">
                        <div class="min-w-0">
                            <div class="text-[11px] text-gray-500">Jenis</div>
                            <div class="text-gray-800 break-words">{{ $jenis }}</div>
                            <div class="text-[11px] text-gray-400">ID: {{ $req->id }}</div>
                        </div>

                        <div class="min-w-0">
                            <div class="text-[11px] text-gray-500">Judul/Subjek</div>
                            <div class="text-gray-800 break-words">{{ $judul }}</div>
                        </div>

                        <div class="text-[12px] text-gray-500">
                            Diajukan: {{ $tanggal }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('service-requests.show', $req->id) }}"
                           class="w-full inline-flex justify-center items-center px-3 py-2 rounded-lg text-xs font-semibold border border-sky-200 text-sky-700 hover:bg-sky-50">
                            Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-sm text-gray-400">
                    Belum ada pengajuan.
                </div>
            @endforelse
        </div>

        {{-- ===== TABLET/DESKTOP: TABLE ===== --}}
        <div class="overflow-x-auto hidden md:block">
            <table class="homi-table text-sm text-left text-gray-800 min-w-[980px] w-full">
                <thead>
                    <tr>
                        <th>Warga</th>
                        <th>Jenis</th>
                        <th>Judul/Subjek</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $req)
                        <tr>
                            <td class="align-top">
                                <div class="font-medium text-[13px] text-gray-900 max-w-[260px] truncate">
                                    {{ $req->user->full_name ?? $req->user->name ?? $req->user->username ?? '-' }}
                                </div>
                                <div class="text-[11px] text-gray-500 max-w-[260px] truncate">
                                    {{ $req->user->email ?? '-' }}
                                </div>
                            </td>

                            <td class="align-top">
                                <div class="text-[13px] text-gray-800 max-w-[220px] truncate">
                                    {{ $req->type->name ?? '-' }}
                                </div>
                                <div class="text-[11px] text-gray-400">ID: {{ $req->id }}</div>
                            </td>

                            <td class="align-top text-[12px]">
                                <div class="max-w-[340px] truncate">
                                    {{ $req->subject ?? $req->title ?? '-' }}
                                </div>
                            </td>

                            <td class="align-top">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $req->status_badge_class ?? '' }}">
                                    {{ $req->status_label ?? $req->status }}
                                </span>
                            </td>

                            <td class="align-top text-[12px] whitespace-nowrap">
                                {{ optional($req->created_at)->format('d M Y H:i') }}
                            </td>

                            <td class="align-top text-right whitespace-nowrap">
                                <a href="{{ route('service-requests.show', $req->id) }}"
                                   class="text-[12px] text-[var(--homi-blue)] hover:underline font-medium">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-sm text-gray-400">
                                Belum ada pengajuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-1">
            {{ $items->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
