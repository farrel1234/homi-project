<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ResidentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminResidentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        $residents = User::where('role', 'resident')
            ->with('residentProfile')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhereHas('residentProfile', function ($p) use ($q) {
                          $p->where('blok', 'like', "%{$q}%")
                            ->orWhere('no_rumah', 'like', "%{$q}%")
                            ->orWhere('alamat', 'like', "%{$q}%");
                      });
                });
            })
            ->orderBy('name')
            ->paginate(20);

        return response()->json($residents);
    }

    public function show(User $user)
    {
        $user->load('residentProfile');
        return response()->json($user);
    }

    public function upsertAddress(Request $request, User $user)
    {
        $data = $request->validate([
            'blok' => 'nullable|string',
            'no_rumah' => 'nullable|string',
            'alamat' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);

        $profile = ResidentProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json([
            'message' => 'Alamat berhasil disimpan',
            'data' => $profile,
        ]);
    }

    public function updateVisibility(Request $request, User $user)
    {
        $data = $request->validate([
            'is_public' => 'required|boolean',
        ]);

        $profile = ResidentProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['is_public' => $data['is_public']]
        );

        return response()->json($profile);
    }

    public function destroyProfile(User $user)
    {
        ResidentProfile::where('user_id', $user->id)->delete();
        return response()->json(['message' => 'Profile dihapus']);
    }

    // =============================
    // CSV IMPORT (ADMIN)
    // =============================
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = array_map('strtolower', fgetcsv($handle));

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);

                if (!$data['email'] || !$data['name']) continue;

                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'name' => $data['name'],
                        'password' => bcrypt(Str::random(10)),
                        'role' => 'resident',
                    ]
                );

                ResidentProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'blok' => $data['blok'] ?? null,
                        'no_rumah' => $data['no_rumah'] ?? null,
                        'alamat' => $data['alamat'] ?? null,
                        'is_public' => filter_var($data['is_public'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    ]
                );
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        fclose($handle);

        return response()->json(['message' => 'Import CSV berhasil']);
    }
}
