<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Video;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheKeyService;

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

        $user = $request->user();
        if ($user->folders()->count() >= $user->max_folders) {
            return back()->with('error', 'Batasan folder tercapai. Maksimal ' . $user->max_folders . ' folder.');
        }

        $slug = $this->generateUniqueSlug($request->name);

        $request->user()->folders()->create([
            'name' => $request->name,
            'slug' => $slug,
            'is_public' => true,
        ]);

        return back()->with('success', 'Folder berhasil dibuat.');
    }

    public function update(Request $request, Folder $folder)
    {
        if ((int) $folder->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $oldSlug = $folder->slug;
        $newName = $request->name;

        // Update slug if name changes
        if ($folder->name !== $newName) {
            $folder->slug = $this->generateUniqueSlug($newName);
        }

        $folder->name = $newName;
        $folder->save();

        // Invalidate cache for BOTH old and new slugs to be safe
        Cache::forget(CacheKeyService::folderVideos($oldSlug));
        if ($oldSlug !== $folder->slug) {
            Cache::forget(CacheKeyService::folderVideos($folder->slug));
        }

        return back()->with('success', 'Folder berhasil diperbarui.');
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name) . '-' . Str::random(6);
        while (Folder::where('slug', $slug)->exists()) {
            $slug = Str::slug($name) . '-' . Str::random(6);
        }
        return $slug;
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        if ((int) $folder->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $folder->delete();

        // Invalidate cache
        Cache::forget(CacheKeyService::folderVideos($folder->slug));

        return redirect()->route('videos.index')->with('success', 'Folder berhasil dihapus.');
    }

    /**
     * Display the public folder page.
     */
    public function showPublic($slug)
    {
        $folder = Folder::with('user')
            ->where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        $videos = $folder->videos()
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('folders.public', compact('folder', 'videos'));
    }
}
