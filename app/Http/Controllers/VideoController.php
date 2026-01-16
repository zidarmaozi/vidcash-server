<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    /**
     * Menampilkan halaman kelola link dengan filter dan pagination.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = $request->query('per_page', 10);

        $folderId = $request->query('folder_id');

        $videosQuery = $user->videos()->withCount([
            'views' => function ($query) {
                $query->where('validation_passed', true);
            }
        ]);

        if ($folderId) {
            $videosQuery->where('folder_id', $folderId);
        }

        if ($search) {
            // Pencarian sekarang menggunakan accessor, jadi kita cari di video_code
            $videosQuery->where('video_code', 'like', "%{$search}%");
        }

        $videosQuery->orderBy($sortBy, $sortDir);

        $videos = $videosQuery->paginate($perPage);
        $folders = $user->folders()->get();

        return view('videos.index', [
            'videos' => $videos,
            'folders' => $folders,
            'currentFolder' => $folderId ? $folders->find($folderId) : null,
            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
                'folder_id' => $folderId,
            ]
        ]);
    }

    /**
     * Menampilkan halaman untuk membuat video baru.
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Menyimpan video baru dari form "Generate dari URL".
     */
    public function store(Request $request)
    {
        $validated = $request->validate(['originalUrls' => 'required|string']);
        $urls = preg_split('/\\r\\n|\\r|\\n/', $validated['originalUrls']);

        $newlyCreatedVideos = collect();
        $errors = [];

        foreach ($urls as $originalUrl) {
            $trimmedUrl = trim($originalUrl);
            if (empty($trimmedUrl))
                continue;

            $videoId = null;
            if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $trimmedUrl, $matches)) {
                $videoId = $matches[1];
            }

            if ($videoId) {
                $existingVideo = Video::where('video_code', $videoId)->first();
                if ($existingVideo && $existingVideo->user_id !== Auth::id()) {
                    $errors[] = "Link dengan ID {$videoId} sudah diklaim pengguna lain.";
                    continue;
                } elseif ($existingVideo) {
                    $newlyCreatedVideos->push($existingVideo);
                    continue;
                }

                $newVideo = Video::create([
                    'user_id' => Auth::id(),
                    'title' => 'Video ' . $videoId,
                    'original_link' => $trimmedUrl,
                    'video_code' => $videoId,
                ]);
                $newlyCreatedVideos->push($newVideo);
            }
        }

        return redirect()->route('videos.create')
            ->with('newly_created_videos', $newlyCreatedVideos)
            ->with('claim_errors', $errors);
    }

    /**
     * Menerima link dari JavaScript setelah upload berhasil.
     */
    public function saveLinkFromApi(Request $request)
    {
        $validated = $request->validate(['original_url' => 'required|url']);
        $originalUrl = $validated['original_url'];

        parse_str(parse_url($originalUrl, PHP_URL_QUERY), $queryParams);
        $videoId = $queryParams['id'] ?? null;

        if (!$videoId) {
            return response()->json(['message' => 'ID Video tidak valid.'], 422);
        }

        $existingVideo = Video::where('video_code', $videoId)->first();
        if ($existingVideo && $existingVideo->user_id !== Auth::id()) {
            return response()->json(['message' => 'Link ini sudah diklaim oleh pengguna lain.'], 409);
        } elseif ($existingVideo) {
            return response()->json(['message' => 'Link ini sudah ada di koleksi Anda.', 'video' => $existingVideo]);
        }

        try {
            $newVideo = Video::create([
                'user_id' => Auth::id(),
                'title' => 'Upload ' . $videoId,
                'original_link' => $originalUrl,
                'video_code' => $videoId,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error saat menyimpan ke database.'], 500);
        }

        return response()->json([
            'message' => 'Link berhasil disimpan.',
            'video' => $newVideo
        ]);
    }

    public function generateFromLinks(Request $request)
    {
        $validated = $request->validate([
            'urls' => 'required|array',
            'urls.*' => 'string',
        ]);

        $urls = $validated['urls'];
        $newlyCreatedVideos = collect();
        $errors = [];

        foreach ($urls as $originalUrl) {
            $trimmedUrl = trim($originalUrl);
            if (empty($trimmedUrl))
                continue;

            $videoId = null;
            if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $trimmedUrl, $matches)) {
                $videoId = $matches[1];
            }

            if ($videoId) {
                $existingVideo = Video::where('video_code', $videoId)->first();
                if ($existingVideo && $existingVideo->user_id !== Auth::id()) {
                    $errors[] = "Link dengan ID {$videoId} sudah diklaim pengguna lain.";
                    continue;
                } elseif ($existingVideo) {
                    $newlyCreatedVideos->push($existingVideo);
                    continue;
                }

                $newVideo = Video::create([
                    'user_id' => Auth::id(),
                    'title' => 'Video ' . $videoId,
                    'original_link' => $trimmedUrl,
                    'video_code' => $videoId,
                ]);
                $newlyCreatedVideos->push($newVideo);
            } else {
                $errors[] = "Format URL tidak valid: " . Str::limit($trimmedUrl, 30);
            }
        }

        return response()->json([
            'new_videos' => $newlyCreatedVideos,
            'errors' => $errors,
        ]);
    }

    /**
     * Update video title.
     */
    public function update(Request $request, Video $video)
    {
        if (Auth::id() !== (int) $video->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $video->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Judul video berhasil diperbarui.',
                'video' => $video
            ]);
        }

        return redirect()->route('videos.index')->with('success', 'Judul video berhasil diperbarui.');
    }

    /**
     * Menghapus video.
     */
    public function destroy(Video $video)
    {
        if (Auth::id() !== $video->user_id) {
            abort(403, 'Unauthorized action.');
        }
        $video->delete();
        return redirect()->route('videos.index')->with('success', 'Video berhasil dihapus.');
    }

    /**
     * Menangani aksi bulk (pilih banyak).
     */
    // app/Http/Controllers/VideoController.php

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:delete,move', // Hanya izinkan aksi 'delete' atau 'move'
            'video_ids' => 'required|array',
            'video_ids.*' => 'exists:videos,id',
        ]);

        $videoIds = $validated['video_ids'];

        // Pastikan video milik user yang sedang login
        $videos = Auth::user()->videos()->whereIn('id', $videoIds);

        if ($validated['action'] === 'delete') {
            $videos->delete();
            return back()->with('success', count($videoIds) . ' link berhasil dihapus.');
        }

        if ($validated['action'] === 'move') {
            $request->validate([
                'folder_id' => 'required|exists:folders,id,user_id,' . Auth::id(),
            ]);

            $videos->update(['folder_id' => $request->folder_id]);
            return back()->with('success', count($videoIds) . ' link berhasil dipindahkan.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }
}
