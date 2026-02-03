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
            'status' => true,
            'message' => 'Profil alamat berhasil disimpan',
            'data' => $profile
        ]);
    }
}
