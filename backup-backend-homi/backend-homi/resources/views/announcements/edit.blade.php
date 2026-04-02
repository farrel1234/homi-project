@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="homi-card p-0 overflow-hidden shadow-2xl shadow-slate-200">
        {{-- Header --}}
        <div class="bg-slate-900 p-8 text-white relative">
            <div class="absolute right-0 top-0 p-8 opacity-10">
                <svg viewBox="0 0 24 24" class="h-24 w-24 fill-current"><path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-2xl font-black tracking-tight mb-2 uppercase">Edit Pengumuman</h1>
                <p class="text-slate-400 text-sm font-medium">Perbarui informasi pengumuman Hawaii Garden</p>
            </div>
        </div>

        <form action="{{ route('announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            {{-- Row 1: Judul --}}
            <div class="space-y-2">
                <label class="homi-label text-slate-900">Judul Pengumuman</label>
                <input type="text" name="title" value="{{ old('title', $announcement->title) }}" placeholder="Ketikkan judul yang menarik..." 
                       class="homi-input text-lg font-bold placeholder:font-normal" required>
            </div>

            {{-- Row 2: Kategori & Tanggal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="homi-label">Kategori</label>
                    <select name="category" class="homi-input font-bold">
                        <option value="Informasi Umum" @selected(old('category', $announcement->category) == 'Informasi Umum')>Informasi Umum</option>
                        <option value="Keamanan" @selected(old('category', $announcement->category) == 'Keamanan')>Keamanan</option>
                        <option value="Kegiatan" @selected(old('category', $announcement->category) == 'Kegiatan')>Kegiatan</option>
                        <option value="Pembangunan" @selected(old('category', $announcement->category) == 'Pembangunan')>Pembangunan</option>
                        <option value="Lainnya" @selected(old('category', $announcement->category) == 'Lainnya')>Lainnya</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="homi-label">Tanggal Terbit</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}" 
                           class="homi-input font-mono">
                </div>
            </div>

            {{-- Row 3: Pengaturan --}}
            <div class="flex flex-wrap items-center gap-4 bg-slate-50 p-6 rounded-[2rem] border border-slate-100 shadow-inner">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned', $announcement->is_pinned)) 
                           class="h-5 w-5 rounded-lg border-slate-300 text-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">
                    <div class="flex flex-col">
                        <span class="text-sm font-black text-slate-700 uppercase tracking-widest leading-none">Sematkan</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase mt-1">Pin di bagian atas daftar</span>
                    </div>
                </label>
                <div class="h-8 w-px bg-slate-200 hidden md:block"></div>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $announcement->is_public)) 
                           class="h-5 w-5 rounded-lg border-slate-300 text-emerald-500 focus:ring-emerald-500">
                    <div class="flex flex-col">
                        <span class="text-sm font-black text-slate-700 uppercase tracking-widest leading-none">Aktif / Publik</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase mt-1">Dapat dilihat oleh warga</span>
                    </div>
                </label>
            </div>

            {{-- Row 4: Isi --}}
            <div class="space-y-2">
                <label class="homi-label">Konten Pengumuman</label>
                <div class="rounded-3xl overflow-hidden border border-slate-200 shadow-inner">
                    <textarea id="editor" name="body" rows="8">{{ old('body', $announcement->body ?? $announcement->content) }}</textarea>
                </div>
            </div>

            {{-- Row 5: Media --}}
            <div class="space-y-4">
                <label class="homi-label">Gambar Utama / Cover</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div class="relative group">
                        <input type="file" id="image-input" name="image" accept="image/*" class="hidden">
                        <label for="image-input" class="flex flex-col items-center justify-center w-full h-48 border-4 border-dashed border-slate-200 rounded-[2.5rem] cursor-pointer bg-slate-50 hover:bg-white hover:border-[var(--homi-blue)] transition-all group-hover:shadow-[0_20px_40px_rgba(31,111,139,0.1)]">
                            <div class="flex flex-col items-center justify-center p-6 text-center">
                                <div class="w-16 h-16 rounded-3xl bg-white border border-slate-100 shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 group-hover:rotate-6 transition-transform">
                                    <svg class="w-8 h-8 text-[var(--homi-blue)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Ganti Gambar</p>
                                <p class="text-[10px] text-slate-400 mt-2">Format JPEG, PNG, WEBP (Maks 2MB)</p>
                            </div>
                        </label>
                    </div>

                    <div id="image-preview-container" class="{{ $announcement->image_path ? '' : 'hidden' }} relative group">
                        <div class="absolute -top-3 -right-3 z-10">
                            <button type="button" id="remove-image" class="bg-rose-500 text-white rounded-2xl p-2 shadow-xl hover:bg-rose-600 hover:rotate-90 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="rounded-[2.5rem] overflow-hidden border-4 border-white shadow-2xl h-48 w-full">
                            <img id="image-preview" src="{{ $announcement->image_path ? asset('storage/'.$announcement->image_path) : '#' }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
                @error('image')
                    <p class="text-rose-500 text-xs font-bold mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Row 6: Actions --}}
            <div class="flex flex-col-reverse sm:flex-row justify-end items-center gap-4 pt-8 border-t border-slate-100">
                <a href="{{ route('announcements.index') }}" class="w-full sm:w-auto text-center px-10 py-3 rounded-2xl text-sm font-black text-slate-400 uppercase tracking-[0.2em] hover:text-slate-600 transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-12 py-4 rounded-[1.5rem] bg-slate-900 text-white text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:shadow-[var(--homi-blue-light)] hover:bg-[var(--homi-blue)] transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>


<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: {
            items: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'
            ]
        }
    }).catch(console.error);

    // Image Preview Script
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');
    const previewContainer = document.getElementById('image-preview-container');
    const removeBtn = document.getElementById('remove-image');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    removeBtn.addEventListener('click', function() {
        imageInput.value = '';
        if ("{{ $announcement->image_path }}" === "") {
            previewContainer.classList.add('hidden');
        }
        imagePreview.src = "{{ $announcement->image_path ? asset('storage/'.$announcement->image_path) : '#' }}";
    });
</script>

<style>
    .ck.ck-editor { width: 100% !important; }
    .ck.ck-editor__main { width: 100% !important; }
    .ck.ck-editor__editable { min-height: 250px; border-bottom-left-radius: 12px !important; border-bottom-right-radius: 12px !important; }
    .ck.ck-toolbar { border-top-left-radius: 12px !important; border-top-right-radius: 12px !important; border-color: #e5e7eb !important; background: #f9fafb !important; }
</style>
@endsection
