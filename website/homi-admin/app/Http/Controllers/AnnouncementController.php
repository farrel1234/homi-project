<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $announcements = Announcement::when($q, function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('announcements.index', compact('announcements', 'q'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
            'start_at'  => 'nullable|date',
            'end_at'    => 'nullable|date',
        ]);

        $data['author_id'] = auth()->id() ?? 1;
        $data['is_public'] = $request->boolean('is_public', true);

        Announcement::create($data);

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show(Announcement $announcement)
    {
        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
            'start_at'  => 'nullable|date',
            'end_at'    => 'nullable|date',
        ]);

        // 🔥 is_public: kalau gak dikirim, pakai nilai lama
        $data['is_public'] = $request->boolean('is_public', $announcement->is_public);

        $announcement->update($data);

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
