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
        $tenantId = $request->user()->tenant_id;

        $announcements = Announcement::whereNotNull('published_at')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('is_pinned')
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
            'tenant_id'    => $user->tenant_id,
            'content'      => $data['content'],
            'is_pinned'    => $request->boolean('is_pinned'),
            'published_at' => now(),
            'created_by'   => $user->id,
            'image_path'   => $imagePath,
        ]);

        // 🔥 Broadcast Kirim Push Notification FCM ke semua user di tenant yang sama
        $firebase = new \App\Services\FirebaseService();
        $users = \App\Models\User::where('tenant_id', $user->tenant_id)
            ->whereNotNull('fcm_token')
            ->get();
        
        $bodyText = strlen($announcement->content) > 60 
            ? substr($announcement->content, 0, 60) . '...' 
            : $announcement->content;

        foreach ($users as $u) {
            $firebase->sendNotification(
                $u->fcm_token,
                "Pengumuman Baru: " . $announcement->title,
                $bodyText
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Pengumuman berhasil dibuat dan notifikasi dikirim.',
            'data'    => $announcement,
        ], 201);
    }

}
