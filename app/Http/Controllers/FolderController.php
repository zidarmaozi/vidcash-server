<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Video;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = Str::slug($request->name) . '-' . Str::random(6);

        $request->user()->folders()->create([
            'name' => $request->name,
            'slug' => $slug,
            'is_public' => true,
        ]);

        return back()->with('success', 'Folder berhasil dibuat.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Folder $folder)
    {
        if ($folder->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder->update([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Folder berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        if ($folder->user_id !== auth()->id()) {
            abort(403);
        }

        $folder->delete();

        return redirect()->route('videos.index')->with('success', 'Folder berhasil dihapus.');
    }

    /**
     * Display the public folder page.
     */
    public function showPublic($slug)
    {
        $folder = Folder::where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        $videos = $folder->videos()
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('folders.public', compact('folder', 'videos'));
    }
}
