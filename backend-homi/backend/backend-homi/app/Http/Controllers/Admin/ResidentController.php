<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ResidentProfile; // <-- sesuaikan kalau nama model berbeda
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $items = ResidentProfile::query()
            ->with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->whereHas('user', function ($u) use ($q) {
                        $u->where('full_name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%")
                          ->orWhere('username', 'like', "%{$q}%");
                    })
                    ->orWhere('no_rumah', 'like', "%{$q}%")
                    ->orWhere('blok', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('residents.index', compact('items', 'q'));
    }

    public function create()
    {
        return view('residents.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'email'     => ['required','email','max:255','unique:users,email'],
            'phone'     => ['nullable','string','max:30'],
            'blok'      => ['nullable','string','max:50'],
            'no_rumah'  => ['nullable','string','max:50'],
            'is_public' => ['nullable','boolean'],

            // optional (kalau suatu saat mau input manual)
            'username'  => ['nullable','string','max:255','unique:users,username'],
        ]);

        DB::transaction(function () use ($data) {

            // ===== USERNAME: kalau kosong, buat otomatis dari email =====
            $username = $data['username'] ?? null;

            if (!$username) {
                $base = Str::before($data['email'], '@');
                $base = Str::slug($base, ''); // buang simbol, tanpa dash
                $base = $base ?: 'user';

                $candidate = $base;
                $i = 0;
                while (User::where('username', $candidate)->exists()) {
                    $i++;
                    $candidate = $base . $i;
                }
                $username = $candidate;
            }

            // ===== PASSWORD: wajib diisi karena kolom users.password NOT NULL =====
            // biar aman: random, warga bisa set lewat "lupa password" nanti
            $randomPassword = Str::random(12);

            $user = User::create([
                'full_name'   => $data['full_name'],
                'name'        => $data['full_name'],
                'username'    => $username,
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? null,
                'role_id'     => 2,
                'role'        => 'resident',
                'is_active'   => 1,
                'is_verified' => 1,
                'password'    => Hash::make($randomPassword),
            ]);

            ResidentProfile::create([
                'user_id'   => $user->id,
                'blok'      => $data['blok'] ?? null,
                'no_rumah'  => $data['no_rumah'] ?? null,
                'is_public' => (bool)($data['is_public'] ?? false),
            ]);
        });

        return redirect()
            ->route('residents.index')
            ->with('ok', 'Warga berhasil ditambahkan.');
    }

    public function edit(ResidentProfile $resident)
    {
        return view('residents.edit', [
            'item' => $resident->load('user'),
        ]);
    }

    public function update(Request $request, ResidentProfile $resident)
    {
        $user = $resident->user;

        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'username'  => ['nullable','string','max:255','unique:users,username,' . ($user?->id ?? 'NULL')],
            'email'     => ['required','email','max:255','unique:users,email,' . ($user?->id ?? 'NULL')],
            'phone'     => ['nullable','string','max:30'],
            'blok'      => ['nullable','string','max:50'],
            'no_rumah'  => ['nullable','string','max:50'],
            'is_public' => ['nullable','boolean'],
        ]);

        DB::transaction(function () use ($resident, $user, $data) {
            if ($user) {
                $user->update([
                    'full_name' => $data['full_name'],
                    'name'      => $data['full_name'],
                    'username'  => $data['username'] ?? null,
                    'email'     => $data['email'],
                    'phone'     => $data['phone'] ?? null,
                ]);
            }

            $resident->update([
                'blok'      => $data['blok'] ?? null,
                'no_rumah'  => $data['no_rumah'] ?? null,
                'is_public' => (bool)($data['is_public'] ?? false),
            ]);
        });

        return redirect()
            ->route('residents.index')
            ->with('ok', 'Data warga berhasil diperbarui.');
    }

    public function destroy(ResidentProfile $resident)
    {
        $resident->delete();

        return redirect()
            ->route('residents.index')
            ->with('ok', 'Data warga berhasil dihapus dari direktori.');
    }
}
