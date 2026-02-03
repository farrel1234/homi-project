@extends('layouts.app')

@section('title', 'Tambah Pengumuman')

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
    <div class="homi-card">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <div class="homi-title">Tambah Pengumuman</div>
                <div class="homi-subtitle">Buat pengumuman baru untuk warga.</div>
            </div>

            <a href="{{ route('announcements.index') }}"
               class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-[var(--homi-border)] text-sm hover:bg-gray-50">
                Kembali
            </a>
        </div>

        {{-- Error validation --}}
        @if($errors->any())
            <div class="mt-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
                <div class="font-semibold mb-1">Periksa input:</div>
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('announcements.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="mt-6 space-y-4">
            @csrf

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
                <input type="text"
                       name="title"
                       value="{{ old('title') }}"
                       class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-sky-200"
                       required>
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori (Opsional)</label>
                <input type="text"
                       name="category"
                       value="{{ old('category') }}"
                       class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-sky-200">
            </div>

            {{-- Isi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Pengumuman</label>
                <textarea id="editor"
                          name="body"
                          rows="8"
                          class="w-full border border-[var(--homi-border)] rounded-lg px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-sky-200">{{ old('body') }}</textarea>
                <div class="text-xs text-gray-500 mt-1">Gunakan editor untuk format teks.</div>
            </div>

            {{-- Gambar --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar (Opsional)</label>
                <input type="file"
                       name="image"
                       accept="image/*"
                       class="w-full border border-[var(--homi-border)] rounded-lg p-2 bg-white text-sm">
                <div class="text-xs text-gray-500 mt-1">Format: jpg/png/webp, ukuran menyesuaikan.</div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2">
                <a href="{{ route('announcements.index') }}"
                   class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-[var(--homi-border)] text-sm hover:bg-gray-50">
                    Batal
                </a>

                <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 rounded-lg bg-[var(--homi-orange)] text-white text-sm font-semibold hover:bg-orange-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#editor')).catch(console.error);
</script>

{{-- CSS biar CKEditor RESPONSIVE --}}
<style>
    .ck.ck-editor { width: 100% !important; }
    .ck.ck-editor__main { width: 100% !important; }
    .ck.ck-editor__editable { min-height: 220px; }

    /* Toolbar auto wrap + kalau kepanjangan bisa scroll */
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

    /* Biar table/gambar/video di editor gak bikin layar melebar */
    .ck-content table, .ck-content img, .ck-content iframe {
        max-width: 100% !important;
    }
</style>
@endsection
