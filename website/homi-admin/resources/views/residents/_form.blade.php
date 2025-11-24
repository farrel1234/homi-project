@csrf

<div class="grid md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm font-medium mb-1">User (Akun)</label>
    <select name="user_id" class="border rounded p-2 w-full" required>
      <option value="">-- Pilih User --</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}"
          @selected(old('user_id', $resident->user_id ?? null) == $u->id)>
          {{ $u->name }} ({{ $u->email }})
        </option>
      @endforeach
    </select>
    @error('user_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Nama Warga</label>
    <input type="text" name="name" class="border rounded p-2 w-full"
           value="{{ old('name', $resident->name ?? '') }}" required>
    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">No Rumah / Blok</label>
    <input type="text" name="house_number" class="border rounded p-2 w-full"
           value="{{ old('house_number', $resident->house_number ?? '') }}">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Kepala Keluarga</label>
    <input type="text" name="family_head" class="border rounded p-2 w-full"
           value="{{ old('family_head', $resident->family_head ?? '') }}">
  </div>
</div>

<div class="mt-4">
  <label class="block text-sm font-medium mb-1">Alamat</label>
  <textarea name="address" rows="3" class="border rounded p-2 w-full">{{ old('address', $resident->address ?? '') }}</textarea>
</div>

<div class="mt-4 grid md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm font-medium mb-1">No. Identitas (KTP / KK)</label>
    <input type="text" name="id_number" class="border rounded p-2 w-full"
           value="{{ old('id_number', $resident->id_number ?? '') }}">
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Info Lain</label>
    <input type="text" name="other_info" class="border rounded p-2 w-full"
           value="{{ old('other_info', $resident->other_info ?? '') }}">
  </div>
</div>

<div class="mt-6 flex justify-end gap-2">
  <a href="{{ route('residents.index') }}" class="px-4 py-2 rounded border text-sm">Batal</a>
  <button class="px-4 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">
    {{ $submitLabel ?? 'Simpan' }}
  </button>
</div>
