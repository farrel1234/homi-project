@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="max-w-3xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
    <div class="homi-card">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div class="min-w-0">
                <div class="homi-title">Edit Pengumuman</div>
                <div class="homi-subtitle">Perbarui informasi pengumuman.</div>
            </div>

            <a href="{{ route('announcements.index') }}"
               class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-[var(--homi-border)] text-sm hover:bg-gray-50">
                Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-800 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('announcements.update', $announcement) }}"
              method="POST" enctype="multipart/form-data"
              class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
                <input type="text" name="title"
                       value="{{ old('title', $announcement->title) }}"
                       class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-sky-200"
                       required>
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori (Opsional)</label>
                <input type="text" name="category"
                       value="{{ old('category', $announcement->category) }}"
                       class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-sky-200">
            </div>

            {{-- Isi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Pengumuman</label>
                <textarea id="editor" name="body" rows="8"
                          class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-sky-200"
                          required>{{ old('body', $announcement->body) }}</textarea>
            </div>

            {{-- Gambar --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar (Opsional)</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full border border-[var(--homi-border)] rounded-lg p-2 bg-white text-sm">

                @if($announcement->image_path)
                    <div class="mt-3">
                        <p class="text-sm text-gray-600">Gambar saat ini:</p>
                        <img src="{{ asset('storage/'.$announcement->image_path) }}"
                             class="mt-2 w-full max-w-sm sm:max-w-xs h-auto object-cover rounded-xl border border-gray-100"
                             alt="Gambar Pengumuman">
                    </div>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2">
                <a href="{{ route('announcements.index') }}"
                   class="w-full sm:w-auto text-center px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200">
                    Batal
                </a>

                <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 bg-[var(--homi-blue)] text-white rounded-lg hover:opacity-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#editor')).catch(console.error);
</script>

{{-- CSS biar CKEditor RESPONSIVE --}}
<style>
    .ck.ck-editor { width: 100% !important; }
    .ck.ck-editor__main { width: 100% !important; }
    .ck.ck-editor__editable { min-height: 220px; }

    .ck.ck-toolbar {
        flex-wrap: wrap !important;
        overflow-x: auto;
    }
    .ck.ck-toolbar .ck-toolbar__items {
        flex-wrap: wrap !important;
    }
    .ck.ck-toolbar .ck-toolbar__items > * {
        margin-top: 4px;
    }

    .ck-content table, .ck-content img, .ck-content iframe {
        max-width: 100% !important;
    }
</style>
@endsection
