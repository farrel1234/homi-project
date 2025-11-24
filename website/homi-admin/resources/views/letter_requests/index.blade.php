@extends('layouts.app')

@section('title','Pengajuan Surat')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Pengajuan Surat</h1>
        <p class="homi-subtitle">
            Kelola pengajuan surat yang diajukan warga. Admin dapat memproses dan mengunduh surat dalam bentuk PDF.
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

    {{-- PANEL UTAMA --}}
    <div class="homi-card space-y-4">

        {{-- FILTER BAR --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">
                    Daftar Pengajuan Surat
                </h2>
                <p class="text-[12px] text-gray-500">
                    Filter berdasarkan status atau cari berdasarkan nama warga / jenis surat.
                </p>
            </div>

            <form method="GET"
                  action="{{ route('letter-requests.index') }}"
                  class="flex flex-col md:flex-row gap-2 md:items-center text-sm">
                <select name="status"
                        class="rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                    <option value="">Semua status</option>
                    <option value="submitted"  @selected($status=='submitted')>Diajukan</option>
                    <option value="processed"  @selected($status=='processed')>Diproses</option>
                    <option value="approved"   @selected($status=='approved')>Disetujui</option>
                    <option value="rejected"   @selected($status=='rejected')>Ditolak</option>
                </select>

                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       placeholder="Cari nama warga / jenis surat..."
                       class="w-full md:w-72 rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">

                <button
                    class="px-4 py-2 rounded-xl bg-[var(--homi-blue)] text-white hover:bg-sky-800 font-medium">
                    Cari
                </button>

                @if($status || $q)
                    <a href="{{ route('letter-requests.index') }}"
                       class="text-xs text-gray-500 hover:underline">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- TABEL --}}
        <div class="overflow-x-auto">
            <table class="homi-table text-sm text-left text-gray-800">
                <thead>
                    <tr>
                        <th>Warga</th>
                        <th>Jenis Surat</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $req)
                        <tr>
                            <td class="align-top">
                                <div class="font-medium text-[13px] text-gray-900">
                                    {{ $req->user->full_name ?? $req->user->username ?? '-' }}
                                </div>
                                <div class="text-[11px] text-gray-500">
                                    {{ $req->user->email ?? '-' }}
                                </div>
                            </td>
                            <td class="align-top">
                                <div class="text-[13px] text-gray-800">
                                    {{ $req->type->name ?? '-' }}
                                </div>
                                <div class="text-[11px] text-gray-400">
                                    ID: {{ $req->id }}
                                </div>
                            </td>
                            <td class="align-top">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $req->status_badge_class }}">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            <td class="align-top text-[12px]">
                                {{ $req->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="align-top text-right whitespace-nowrap">
                                <a href="{{ route('letter-requests.show', $req->id) }}"
                                   class="text-[12px] text-[var(--homi-blue)] hover:underline font-medium">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-sm text-gray-400">
                                Belum ada pengajuan surat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINASI --}}
        <div class="pt-1">
            {{ $items->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
