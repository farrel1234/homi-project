<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    // LIST PENGUMUMAN
    public function index(Request $request)
    {
        $q = $request->query('q');

        $announcements = Announcement::query()
            ->when($q, function ($query) use ($q) {
                $query->where('title', 'like', '%' . $q . '%');
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('announcements.index', [
            'announcements' => $announcements,
            'q'             => $q,
        ]);
    }

    // FORM TAMBAH
    public function create()
    {
        return view('announcements.create');
    }

    // SIMPAN PENGUMUMAN BARU
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'category'    => 'nullable|string|max:100',
            'image'       => 'nullable|image|max:2048',

            // optional kalau view kamu punya inputnya
            'is_pinned'   => 'nullable|boolean',
            'is_public'   => 'nullable|boolean',
            'published_at'=> 'nullable|date',
            'start_at'    => 'nullable|date',
            'end_at'      => 'nullable|date',
        ]);

        // Upload image (optional)
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        // ✅ WAJIB: kolom created_by tidak boleh null
        $data['created_by'] = auth()->id();

        // ✅ Konsisten: content ikut body (mobile pakai content)
        $data['content'] = $data['body'];

        // ✅ default kalau tidak dikirim dari form
        $data['is_pinned'] = (bool)($data['is_pinned'] ?? false);
        $data['is_public'] = (bool)($data['is_public'] ?? true);

        /**
         * ✅ INI YANG PENTING:
         * Kalau form admin kamu belum punya field published_at,
         * maka published_at akan NULL (draft) dan mobile gak akan nampilin.
         *
         * Jadi kita auto publish kalau:
         * - is_public true
         * - dan published_at kosong
         */
        if (($data['is_public'] ?? true) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        Announcement::create($data);

        return redirect()->route('announcements.index')
            ->with('ok', 'Pengumuman berhasil dibuat.');
    }

    // FORM EDIT
    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    // UPDATE PENGUMUMAN
    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'category'    => 'nullable|string|max:100',
            'image'       => 'nullable|image|max:2048',

            // optional kalau view kamu punya inputnya
            'is_pinned'   => 'nullable|boolean',
            'is_public'   => 'nullable|boolean',
            'published_at'=> 'nullable|date',
            'start_at'    => 'nullable|date',
            'end_at'      => 'nullable|date',
        ]);

        // Upload image baru (optional)
        if ($request->hasFile('image')) {
            if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            $data['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        // ✅ Konsisten: content ikut body
        $data['content'] = $data['body'];

        // ✅ Checkbox yang tidak dikirim jangan timpa value lama (biar aman)
        if (!array_key_exists('is_pinned', $data)) unset($data['is_pinned']);
        if (!array_key_exists('is_public', $data)) unset($data['is_public']);

        // ✅ Auto publish saat update jika public & published_at kosong
        $isPublic = array_key_exists('is_public', $data)
            ? (bool)$data['is_public']
            : (bool)$announcement->is_public;

        $publishedAt = $data['published_at'] ?? $announcement->published_at;

        if ($isPublic && empty($publishedAt)) {
            $data['published_at'] = now();
        }

        $announcement->update($data);

        return redirect()->route('announcements.index')
            ->with('ok', 'Pengumuman berhasil diperbarui.');
    }

    // HAPUS
    public function destroy(Announcement $announcement)
    {
        if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('ok', 'Pengumuman berhasil dihapus.');
    }
}
