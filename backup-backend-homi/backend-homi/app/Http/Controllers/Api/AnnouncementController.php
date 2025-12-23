<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // GET /api/announcements
    public function index(Request $request)
    {
        // misal ambil hanya yang sudah publish, urut terbaru,
        // boleh tambahkan pagination bila perlu
        $announcements = Announcement::whereNotNull('published_at')
            ->orderByDesc('is_pinned')    // pinned diatas
            ->orderByDesc('published_at')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $announcements,
        ]);
    }

    // GET /api/announcements/{id}
    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $announcement,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // pastikan hanya admin yang boleh
        if ($user->role !== 'admin') {
            return response()->json([
                'status'  => false,
                'message' => 'Hanya admin yang dapat membuat pengumuman.',
            ], 403);
        }

        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'is_pinned' => 'nullable|boolean',
            'image'     => 'nullable|image|max:2048', // max ~2MB
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            // file akan disimpan di storage/app/public/announcements
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        $announcement = Announcement::create([
            'title'        => $data['title'],
            'content'      => $data['content'],
            'is_pinned'    => $request->boolean('is_pinned'),
            'published_at' => now(),
            'created_by'   => $user->id,
            'image_path'   => $imagePath,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Pengumuman berhasil dibuat.',
            'data'    => $announcement,
        ], 201);
    }

}
