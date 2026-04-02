@extends('layouts.app')

@section('title', $announcement->title ?? 'Detail Pengumuman')
@section('page_title', 'Detail Pengumuman')
@section('page_subtitle', 'Lihat konten pengumuman secara lengkap')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="homi-card p-4 sm:p-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 mb-1 break-words">
                    {{ $announcement->title }}
                </h1>

                <p class="text-xs text-slate-500">
                    Diposting: {{ $announcement->created_at?->format('d M Y H:i') }}
                    @if($announcement->author)
                        - oleh {{ $announcement->author->full_name ?? $announcement->author->name ?? $announcement->author->username }}
                    @endif
                </p>

                @if($announcement->start_at || $announcement->end_at)
                    <p class="text-xs text-slate-500 mt-1">
                        Periode:
                        {{ optional($announcement->start_at)->format('d M Y') ?? '-' }}
                        -
                        {{ optional($announcement->end_at)->format('d M Y') ?? '-' }}
                    </p>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end gap-2">
                @if($announcement->is_public)
                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Publik
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-gray-50 text-gray-600 border border-gray-200">
                        Draft
                    </span>
                @endif

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                    <a href="{{ route('announcements.edit', $announcement) }}"
                       class="text-xs px-3 py-2 sm:py-1 rounded-lg border border-sky-300 text-sky-700 hover:bg-sky-50
                              text-center w-full sm:w-auto">
                        Edit
                    </a>
                    <a href="{{ route('announcements.index') }}"
                       class="text-xs px-3 py-2 sm:py-1 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50
                              text-center w-full sm:w-auto">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-4 border-slate-200">

        {{-- Isi pengumuman (HTML dari CKEditor) --}}
        <div class="prose max-w-none prose-slate overflow-x-auto">
            {!! $announcement->content ?? $announcement->body !!}
        </div>
    </div>
</div>
@endsection
