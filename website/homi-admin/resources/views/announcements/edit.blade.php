@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h1 class="text-xl font-semibold text-gray-800 mb-1">Edit Pengumuman</h1>
        <p class="text-sm text-gray-500 mb-4">
            Perbarui isi pengumuman.
        </p>

        <form action="{{ route('announcements.update', $announcement) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Judul --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Judul Pengumuman
                </label>
                <input type="text"
                       name="title"
                       value="{{ old('title', $announcement->title) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                       required>
                @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Periode --}}
            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Mulai (opsional)
                    </label>
                    <input type="date"
                           name="start_at"
                           value="{{ old('start_at', optional($announcement->start_at)->format('Y-m-d')) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-sky-500 focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Berakhir (opsional)
                    </label>
                    <input type="date"
                           name="end_at"
                           value="{{ old('end_at', optional($announcement->end_at)->format('Y-m-d')) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-sky-500 focus:border-sky-500">
                </div>
            </div>

            {{-- Checkbox is_public --}}
            <div class="mb-4 flex items-center gap-2">
                <input type="checkbox"
                       id="is_public"
                       name="is_public"
                       value="1"
                       class="rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                       {{ old('is_public', $announcement->is_public) ? 'checked' : '' }}>
                <label for="is_public" class="text-sm text-gray-700">
                    Tampilkan ke semua warga (Publik)
                </label>
            </div>

            {{-- Isi Pengumuman (CKEditor) --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Isi Pengumuman
                </label>

                <textarea id="editor"
                          name="body"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    {!! old('body', $announcement->body) !!}
                </textarea>
                @error('body')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-2 mt-6">
                <a href="{{ route('announcements.index') }}"
                   class="px-4 py-2 text-sm rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-sky-600 text-white font-medium hover:bg-sky-700">
                    Update Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>

{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .then(editor => {
            // tinggi editor 500px
            editor.ui.view.editable.element.style.height = '500px';
        })
        .catch(error => console.error(error));
</script>
@endsection
