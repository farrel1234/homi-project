@extends('layouts.app')

@section('title','Edit Warga')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-1">
        <h1 class="homi-title">Edit Data Warga</h1>
        <p class="homi-subtitle">Perbarui data akun warga dan data rumah.</p>
    </div>

    @if ($errors->any())
        <div class="homi-card bg-rose-50 border border-rose-200 text-sm text-rose-800">
            <div class="font-semibold mb-1">Gagal menyimpan perubahan</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="homi-card border border-gray-200">
        <form method="POST" action="{{ route('residents.update', $item->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-4">

                {{-- USER --}}
                <div class="md:col-span-2">
                    <div class="text-[11px] font-semibold text-gray-500 uppercase">Data Akun</div>
                </div>

                <div>
                    <label class="homi-label">Full Name <span class="text-rose-600">*</span></label>
                    <input type="text" name="full_name"
                           value="{{ old('full_name', optional($item->user)->full_name) }}"
                           class="homi-input"
                           required>
                </div>

                <div>
                    <label class="homi-label">Username</label>
                    <input type="text" name="username"
                           value="{{ old('username', optional($item->user)->username) }}"
                           class="homi-input">
                    <div class="text-[11px] text-gray-500 mt-1">Opsional (boleh dikosongkan).</div>
                </div>

                <div>
                    <label class="homi-label">Email <span class="text-rose-600">*</span></label>
                    <input type="email" name="email"
                           value="{{ old('email', optional($item->user)->email) }}"
                           class="homi-input"
                           required>
                </div>

                <div>
                    <label class="homi-label">No HP</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', optional($item->user)->phone) }}"
                           class="homi-input">
                </div>

                {{-- RESIDENT --}}
                <div class="md:col-span-2 pt-2">
                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100 pb-1 mb-2">Data Rumah</div>
                </div>

                <div>
                    <label class="homi-label">Blok</label>
                    <input type="text" name="blok"
                           value="{{ old('blok', $item->blok ?? $item->block) }}"
                           class="homi-input">
                </div>

                <div>
                    <label class="homi-label">No Rumah</label>
                    <input type="text" name="no_rumah"
                           value="{{ old('no_rumah', $item->no_rumah ?? $item->house_number) }}"
                           class="homi-input">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-start gap-4 rounded-2xl border border-gray-200 p-4 hover:bg-slate-50 transition cursor-pointer">
                        <input type="checkbox" name="is_public" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 text-[var(--homi-blue)] focus:ring-[var(--homi-blue)]"
                               @checked(old('is_public', (bool)($item->is_public ?? false)))>
                        <div>
                            <div class="text-sm font-bold text-gray-900">
                                Tampilkan di Direktori Warga (Publik)
                            </div>
                            <div class="text-[12px] text-gray-500 leading-relaxed">
                                Jika aktif, warga lain dapat melihat data rumah ini di menu direktori aplikasi mobile.
                            </div>
                        </div>
                    </label>
                </div>

            </div>

            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="homi-btn homi-btn-primary px-8">
                    Simpan Perubahan
                </button>

                <a href="{{ route('residents.index') }}"
                   class="homi-btn homi-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
