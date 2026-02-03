@php($u = $item->user ?? null)

<div class="grid md:grid-cols-2 gap-4">

    <div class="md:col-span-2">
        <div class="text-[11px] font-semibold text-gray-500 uppercase mb-2">Data Akun Warga</div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
        <input name="full_name" value="{{ old('full_name', $u->full_name ?? $u->name ?? '') }}"
               class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]" />
        @error('full_name') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Username</label>
        <input name="username" value="{{ old('username', $u->username ?? '') }}"
               class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]" />
        @error('username') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input name="email" type="email" value="{{ old('email', $u->email ?? '') }}"
               class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]" />
        @error('email') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">No HP</label>
        <input name="phone" value="{{ old('phone', $u->phone ?? '') }}"
               class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]" />
        @error('phone') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Password (opsional)</label>
        <input name="password" type="password" placeholder="Kosongkan kalau tidak ingin diubah"
               class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]" />
        <div class="text-[11px] text-gray-500 mt-1">
            Jika kosong saat tambah warga, default: <b>password123</b>
        </div>
        @error('password') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2 mt-2">
        <div class="text-[11px] font-semibold text-gray-500 uppercase mb-2">Data Rumah & Direktori</div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Blok</label>
        <input name="blok" value="{{ old('blok', $item->blok ?? '') }}"
               class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]" />
        @error('blok') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nomor Rumah</label>
        <input name="no_rumah" value="{{ old('no_rumah', $item->no_rumah ?? '') }}"
               class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]" />
        @error('no_rumah') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Alamat</label>
        <textarea name="alamat" rows="3"
                  class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-[var(--homi-blue)] focus:ring-[var(--homi-blue)]">{{ old('alamat', $item->alamat ?? '') }}</textarea>
        @error('alamat') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $item->is_public ?? true)) />
            Tampilkan di direktori warga (publik)
        </label>
    </div>

</div>
