<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResidentProfile;
use App\Models\User;
use Illuminate\Http\Request;

class AdminResidentController extends Controller
{
    /**
     * GET /api/admin/residents?q=
     * List warga + alamat (paginate + search)
     */
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        $residents = User::query()
            ->select('id', 'name', 'email', 'role', 'created_at')
            ->where('role', 'resident')
            ->with('residentProfile:id,user_id,blok,no_rumah,alamat,is_public,updated_at')
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
            ->paginate(20)
            ->through(function ($u) {
                $profile = $u->residentProfile;

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->role,
                    'has_profile' => (bool) $profile,
                    'blok' => $profile?->blok,
                    'no_rumah' => $profile?->no_rumah,
                    'alamat' => $profile?->alamat,
                    'is_public' => $profile?->is_public ?? false,
                    'blok_alamat' => ($profile?->blok && $profile?->no_rumah)
                        ? 'Blok '.$profile->blok.' No '.$profile->no_rumah
                        : ($profile?->alamat ?? null),
                    'profile_updated_at' => $profile?->updated_at,
                ];
            });

        return response()->json($residents);
    }

    /**
     * GET /api/admin/residents/{user}
     * Detail 1 warga
     */
    public function show(User $user)
    {
        if ($user->role !== 'resident') {
            return response()->json(['message' => 'Target harus user resident'], 422);
        }

        $user->load('residentProfile:id,user_id,blok,no_rumah,alamat,is_public,created_at,updated_at');

        $p = $user->residentProfile;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'resident_profile' => $p,
            'blok_alamat' => ($p?->blok && $p?->no_rumah)
                ? 'Blok '.$p->blok.' No '.$p->no_rumah
                : ($p?->alamat ?? null),
        ]);
    }

    /**
     * PUT /api/admin/residents/{user}
     * Upsert alamat warga
     */
    public function upsertAddress(Request $request, User $user)
    {
        if ($user->role !== 'resident') {
            return response()->json(['message' => 'Target harus user resident'], 422);
        }

        $data = $request->validate([
            'blok' => 'nullable|string|max:50',
            'no_rumah' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:255',
            'is_public' => 'nullable|boolean',
        ]);

        $profile = ResidentProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json([
            'message' => 'Alamat warga berhasil disimpan',
            'data' => $profile
        ]);
    }

    /**
     * PATCH /api/admin/residents/{user}/visibility
     * Toggle tampil/tidak di direktori (is_public)
     */
    public function updateVisibility(Request $request, User $user)
    {
        if ($user->role !== 'resident') {
            return response()->json(['message' => 'Target harus user resident'], 422);
        }

        $data = $request->validate([
            'is_public' => 'required|boolean',
        ]);

        $profile = ResidentProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['is_public' => $data['is_public']]
        );

        return response()->json([
            'message' => 'Visibility direktori berhasil diubah',
            'data' => $profile
        ]);
    }

    /**
     * DELETE /api/admin/residents/{user}/profile
     * Hapus alamat/profile warga (reset)
     */
    public function destroyProfile(User $user)
    {
        if ($user->role !== 'resident') {
            return response()->json(['message' => 'Target harus user resident'], 422);
        }

        ResidentProfile::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Resident profile berhasil dihapus'
        ]);
    }
}
