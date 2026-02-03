@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">

    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Edit Pengumuman</h2>

    @if($errors->any())
        <div class="mb-4 px-4 py-2 rounded-lg bg-red-100 text-red-800 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('announcements.update', $announcement) }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Judul</label>
            <input type="text" name="title"
                   value="{{ old('title', $announcement->title) }}"
                   class="w-full border rounded-lg px-3 py-2" required>
        </div>

        {{-- Kategori --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Kategori (Opsional)</label>
            <input type="text" name="category"
                   value="{{ old('category', $announcement->category) }}"
                   class="w-full border rounded-lg px-3 py-2">
        </div>

        {{-- Isi --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Isi Pengumuman</label>
            <textarea id="editor" name="body" rows="6"
                class="w-full border rounded-lg px-3 py-2" required>{{ old('body', $announcement->body) }}</textarea>
        </div>

        {{-- Upload Gambar --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Gambar (Opsional)</label>
            <input type="file" name="image"
                   class="border px-3 py-2 rounded-lg w-full">

            @if($announcement->image_path)
                <p class="text-sm text-gray-600 mt-2">Gambar saat ini:</p>
                <img src="{{ asset('storage/'.$announcement->image_path) }}"
                     class="w-40 h-40 object-cover rounded-lg mt-2">
            @endif
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('announcements.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg">
                Batal
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#editor')).catch(console.error);
</script>
@endsection
