<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResidentProfile;
use Illuminate\Http\Request;

class ResidentProfileController extends Controller
{
    // GET /api/me/resident-profile
    public function show(Request $request)
    {
        $user = $request->user();

        $profile = ResidentProfile::where('user_id', $user->id)->first();

        return response()->json([
            'status' => true,
            'data' => $profile,
        ]);
    }

    // PUT /api/me/resident-profile
    public function upsert(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nik' => 'nullable|string|max:20',
            'blok' => 'nullable|string|max:50',
            'no_rumah' => 'nullable|string|max:50',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'nama_rt' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:100',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string|max:20',
            'house_type' => 'nullable|string|max:50',
            'is_public' => 'nullable|boolean',
        ]);

        // Konversi empty string ke null
        foreach ($data as $k => $v) {
            if ($v === '') $data[$k] = null;
        }

        // Auto-generate alamat
        if (empty($data['alamat']) && !empty($data['blok']) && !empty($data['no_rumah'])) {
            $tenantName = $user->tenant->name ?? 'Perumahan Homi';
            $data['alamat'] = "{$tenantName} Blok {$data['blok']} No {$data['no_rumah']}";
        }

        $profile = ResidentProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json([
            'status' => true,
            'message' => 'Profil alamat berhasil disimpan',
            'data' => $profile
        ]);
    }
}
