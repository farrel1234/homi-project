<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResidentController extends Controller
{
    /**
     * GET /admin/residents?q=
     * List data warga (ambil dari resident_profiles + user)
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $items = Resident::query()
            ->with(['user:id,name,full_name,email,username,phone,role'])
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                      ->orWhere('full_name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('username', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
                })
                ->orWhere('blok', 'like', "%{$q}%")
                ->orWhere('no_rumah', 'like', "%{$q}%")
                ->orWhere('alamat', 'like', "%{$q}%");
            })
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('residents.index', [
            'items' => $items,
            'q' => $q,
        ]);
    }

    /**
     * GET /admin/residents/create
     */
    public function create()
    {
        return view('residents.create');
    }

    /**
     * POST /admin/residents
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email:rfc,dns|max:255|unique:users,email',
            'phone'     => 'nullable|string|max:30',
            'blok'      => 'nullable|string|max:50',
            'no_rumah'  => 'nullable|string|max:50',
            'alamat'    => 'nullable|string|max:255',
            'is_public' => 'nullable|boolean',
        ]);

        // username otomatis dari email
        $baseUsername = Str::slug(Str::before($data['email'], '@'), '');
        $username = $baseUsername ?: 'user';
        $original = $username;
        $suffix = 1;
        while (User::where('username', $username)->exists()) {
            $username = $original . $suffix;
            $suffix++;
        }

        // password random (kalau nanti mau kirim via email/WA terserah)
        $plainPassword = Str::random(10);

        $user = User::create([
            'name'      => $data['full_name'],          // fallback default laravel
            'full_name' => $data['full_name'],          // kalau ada kolom full_name
            'email'     => $data['email'],
            'username'  => $username,                   // kalau ada kolom username
            'phone'     => $data['phone'] ?? null,      // kalau ada kolom phone
            'role'      => 'resident',                  // kalau ada kolom role
            'password'  => Hash::make($plainPassword),
        ]);

        // Buat profile rumah
        Resident::create([
            'user_id'   => $user->id,
            'blok'      => $data['blok'] ?? null,
            'no_rumah'  => $data['no_rumah'] ?? null,
            'alamat'    => $data['alamat'] ?? null,
            'is_public' => (bool)($data['is_public'] ?? false),
        ]);

        return redirect()->route('residents.index')
            ->with('ok', 'Warga berhasil ditambahkan.')
            ->with('plain_password', $plainPassword)
            ->with('new_resident_email', $data['email']);
    }

    /**
     * GET /admin/residents/{resident}/edit
     */
    public function edit(Resident $resident)
    {
        $resident->load('user');
        return view('residents.edit', [
            'item' => $resident,
        ]);
    }

    /**
     * PUT /admin/residents/{resident}
     */
    public function update(Request $request, Resident $resident)
    {
        $resident->load('user');

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => 'nullable|string|max:50',
            'email'     => 'required|email:rfc,dns|max:255|unique:users,email,' . $resident->user_id,
            'phone'     => 'nullable|string|max:30',

            'blok'      => 'nullable|string|max:50',
            'no_rumah'  => 'nullable|string|max:50',
            'alamat'    => 'nullable|string|max:255',
            'is_public' => 'nullable|boolean',
        ]);

        // update user
        $resident->user->update([
            'name'      => $data['full_name'],
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'username'  => $data['username'] ?: null,
            'phone'     => $data['phone'] ?? null,
        ]);

        // update profile
        $resident->update([
            'blok'      => $data['blok'] ?? null,
            'no_rumah'  => $data['no_rumah'] ?? null,
            'alamat'    => $data['alamat'] ?? null,
            'is_public' => (bool)($data['is_public'] ?? false),
        ]);

        return redirect()->route('residents.index')
            ->with('ok', 'Data warga berhasil diperbarui.');
    }

    /**
     * DELETE /admin/residents/{resident}
     * Hapus resident profile + user nya (biar bersih)
     */
    public function destroy(Resident $resident)
    {
        $resident->load('user');
        $user = $resident->user;

        // hapus profile dulu (optional, karena cascade bisa)
        $resident->delete();

        // hapus akun user juga (kalau ini memang yang kamu mau)
        if ($user) $user->delete();

        return redirect()->route('residents.index')
            ->with('ok', 'Warga berhasil dihapus.');
    }

    /**
     * GET /admin/residents/import
     */
    public function importForm()
    {
        return view('residents.import');
    }

    /**
     * GET /admin/residents/template.csv
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_warga.csv"',
        ];

        $rows = [
            ['full_name','email','phone','blok','no_rumah','alamat','is_public'],
            ['Hanif Abyad','hanif@gmail.com','08123456789','A','07','Jl. Hawai Garden','1'],
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $r) fputcsv($out, $r);
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * POST /admin/residents/import
     * Import CSV tanpa package
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membaca file CSV.');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'CSV kosong / header tidak ditemukan.');
        }

        $header = array_map(fn($h) => strtolower(trim((string)$h)), $header);

        $allowed = ['full_name','email','phone','blok','no_rumah','alamat','is_public'];
        foreach ($header as $h) {
            if (!in_array($h, $allowed, true)) {
                fclose($handle);
                return back()->with('error', "Kolom CSV tidak dikenali: {$h}. Gunakan template.");
            }
        }

        $created = 0;
        $skipped = 0;
        $failedRows = [];

        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            $data = [];
            foreach ($header as $i => $key) {
                $data[$key] = isset($row[$i]) ? trim((string)$row[$i]) : null;
            }

            $v = Validator::make($data, [
                'full_name' => 'required|string|max:255',
                'email'     => 'required|email:rfc,dns|max:255',
                'phone'     => 'nullable|string|max:30',
                'blok'      => 'nullable|string|max:50',
                'no_rumah'  => 'nullable|string|max:50',
                'alamat'    => 'nullable|string|max:255',
                'is_public' => 'nullable|in:0,1,true,false,yes,no,ya,tidak',
            ]);

            if ($v->fails()) {
                $failedRows[] = "Baris {$rowNum}: " . $v->errors()->first();
                $skipped++;
                continue;
            }

            // email sudah ada -> skip
            if (User::where('email', $data['email'])->exists()) {
                $skipped++;
                continue;
            }

            $isPublicRaw = strtolower((string)($data['is_public'] ?? '0'));
            $isPublic = in_array($isPublicRaw, ['1','true','yes','ya'], true);

            // username otomatis
            $baseUsername = Str::slug(Str::before($data['email'], '@'), '');
            $username = $baseUsername ?: 'user';
            $original = $username;
            $suffix = 1;
            while (User::where('username', $username)->exists()) {
                $username = $original . $suffix;
                $suffix++;
            }

            $plainPassword = Str::random(10);

            $user = User::create([
                'name'      => $data['full_name'],
                'full_name' => $data['full_name'],
                'email'     => $data['email'],
                'username'  => $username,
                'phone'     => $data['phone'] ?? null,
                'role'      => 'resident',
                'password'  => Hash::make($plainPassword),
            ]);

            Resident::create([
                'user_id'   => $user->id,
                'blok'      => $data['blok'] ?? null,
                'no_rumah'  => $data['no_rumah'] ?? null,
                'alamat'    => $data['alamat'] ?? null,
                'is_public' => $isPublic,
            ]);

            $created++;
        }

        fclose($handle);

        if (!empty($failedRows)) {
            $preview = array_slice($failedRows, 0, 5);
            $msg = "Import selesai. Berhasil: {$created}, Dilewati: {$skipped}. Contoh error: " . implode(' | ', $preview);
            return redirect()->route('residents.index')->with('error', $msg);
        }

        return redirect()->route('residents.index')
            ->with('ok', "Import selesai. Berhasil: {$created}, Dilewati: {$skipped}.");
    }
}
