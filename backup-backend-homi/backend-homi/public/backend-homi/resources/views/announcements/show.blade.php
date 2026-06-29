@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 mb-1">
                    {{ $announcement->title }}
                </h1>
                <p class="text-xs text-gray-500">
                    Diposting: {{ $announcement->created_at?->format('d M Y H:i') }}
                    @if($announcement->author)
                        · oleh {{ $announcement->author->full_name ?? $announcement->author->username }}
                    @endif
                </p>
                @if($announcement->start_at || $announcement->end_at)
                    <p class="text-xs text-gray-500 mt-1">
                        Periode: {{ optional($announcement->start_at)->format('d M Y') ?? '-' }}
                        —
                        {{ optional($announcement->end_at)->format('d M Y') ?? '-' }}
                    </p>
                @endif
            </div>
            <div class="flex flex-col items-end gap-2">
                @if($announcement->is_public)
                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Publik
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-gray-50 text-gray-600 border border-gray-200">
                        Draft
                    </span>
                @endif

                <div class="flex items-center gap-2 mt-1">
                    <a href="{{ route('announcements.edit', $announcement) }}"
                       class="text-xs px-3 py-1 rounded-lg border border-sky-500 text-sky-600 hover:bg-sky-50">
                        Edit
                    </a>
                    <a href="{{ route('announcements.index') }}"
                       class="text-xs px-3 py-1 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-4">

        {{-- Isi pengumuman (HTML dari CKEditor) --}}
        <div class="prose max-w-none prose-sky">
            {!! $announcement->body !!}
        </div>
    </div>
</div>

{{-- Tailwind Typography (kalau belum include di layout) --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/typography/dist/typography.min.css"> --}}
@endsection
