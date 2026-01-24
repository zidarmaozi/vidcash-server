<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Video;
use App\Models\View;
use App\Models\VideoReport;
use App\Models\Folder;
use Cache;
use App\Services\CacheKeyService;
use DB;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Mengirim pengaturan ke service javascript/halam tonton
    public function getSettings($videoCode = null)
    {
        // Cache response selama 10 menit (600 detik)
        $cacheKey = CacheKeyService::settings($videoCode);

        return Cache::remember($cacheKey, 600, function () use ($videoCode) {
            $watchTimeSetting = Setting::where('key', 'watch_time_seconds')->first();
            $video = $videoCode ? Video::where('video_code', $videoCode)->first() : null;

            return response()->json([
                'watch_time_seconds' => $watchTimeSetting ? (int) $watchTimeSetting->value : 10,
                'is_available' => (bool) $video,
                'is_active' => $video ? (bool) $video->is_active : false,
                'video_title' => $video ? $video->title : null,
                'folder' => $video && $video->folder ? [
                    'name' => $video->folder->name,
                    'slug' => $video->folder->slug,
                    'video_count' => $video->folder->videos()->where('is_active', true)->count(),
                ] : null,
            ]);
        });
    }

    public function getFolderVideos($folderSlug)
    {
        $folder = Folder::where('slug', $folderSlug)->first();

        if (!$folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }

        // Cache the video list with the existing key structure
        $videos = Cache::rememberForever(CacheKeyService::folderVideos($folderSlug), function () use ($folder) {
            return $folder->videos()
                ->select('id', 'video_code', 'title', 'thumbnail_path')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return response()->json([
            'name' => $folder->name,
            'slug' => $folder->slug,
            'videos' => $videos
        ]);
    }

    public function getRelatedVideos($videoCode = null)
    {
        // make the cache into 12 hours
        $relatedVideos = Cache::remember(CacheKeyService::relatedVideos($videoCode), 43200, function () {
            return Video::select('id', 'video_code', 'title', 'thumbnail_path')
                ->where('is_active', true)
                ->whereNotNull('thumbnail_path')
                ->where('is_safe_content', true)
                ->inRandomOrder()
                ->take(18)
                ->get();
        });

        return response()->json($relatedVideos);
    }

    public function getRecommendedFolders()
    {
        // Cache for 12 hours
        $recommendedFolders = Cache::remember(CacheKeyService::recommendedFolders(), 43200, function () {
            // "latest 5 folder with minimum videos is 6"
            return Folder::select('id', 'name', 'slug')
                ->where('is_public', true)
                ->has('videos', '>=', 6)
                ->withCount([
                    'videos' => function ($query) {
                        $query->where('is_active', true);
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        });

        return response()->json($recommendedFolders);
    }

    // Menerima perintah untuk mencatat view dari service Node.js
    public function recordView(Request $request)
    {
        $validated = $request->validate([
            'video_code' => 'required|exists:videos,video_code'
        ]);

        $mockResponse = response()->json([
            'message' => 'View recorded successfully.'
        ])->withHeaders(['Accept-State' => '1']);

        $video = Video::where('video_code', $validated['video_code'])->first();
        $ipAddress = $request->ip();
        $currentCpm = $this->getCpm();

        if (!$video) {
            return $mockResponse;
        }

        if ($request->header('accept-portal') !== 'x123' || !$video->is_active) {
            $this->recordFailedView(
                $video->id,
                $ipAddress,
                $currentCpm,
                null,
                -100,
                -100,
                -100
            );
            return $mockResponse;
        }

        $owner = $video->user;
        $validationLevelAdjuster = 0;
        $via = $request->header('x-via');
        $viaResult = null;

        switch ($via) {
            case '1':
                $viaResult = 'direct';
                break;
            case '2':
                $viaResult = 'related';
                $validationLevelAdjuster = -2;
                break;
            case '3':
                $viaResult = 'folder';
                // for early release, validation level will not changed
                // $validationLevel = -1;
                break;
            case '4':
                $viaResult = 'telegram';
                $validationLevelAdjuster = 0;
                break;
            default:
                // block view
                $this->recordFailedView(
                    $video->id,
                    $ipAddress,
                    $currentCpm,
                    $viaResult,
                    -101,
                    -101,
                    -101
                );
                return $mockResponse;
        }

        // 1. Validasi Batas IP
        $ipLimit = $this->getIpLimit();
        $existingViews = View::where('video_id', $video->id)
            ->where('ip_address', $ipAddress)->count();

        if ($existingViews >= $ipLimit) {
            $this->recordFailedView(
                $video->id,
                $ipAddress,
                $currentCpm,
                $viaResult,
                -192,
                -192,
                -192
            );
            return $mockResponse;
        }

        // get last 20 views of the video to check if the user is spamming
        // on range now() - 6 minutes, if it is more than 20, then block the user
        $lastViews = View::where('video_id', $video->id)
            ->where('created_at', '>=', now()->subMinutes(3))
            ->count();

        if ($lastViews >= 3) {
            $this->recordFailedView(
                $video->id,
                $ipAddress,
                $currentCpm,
                $viaResult,
                -99,
                -99,
                -99
            );

            return $mockResponse;
        }

        $originalValidationLevel = 0;
        // 2. Tentukan Level Validasi
        if ($owner->validation_level) {
            $originalValidationLevel = $owner->validation_level;
        } else {
            $originalValidationLevel = (int) (Setting::where('key', 'default_validation_level')->first()->value ?? 5);
        }

        $adjustedValidationLevel = $validationLevelAdjuster + $originalValidationLevel;

        // 4. Validation check
        $viewerValidationLevel = rand(1, 10);
        $validationPassed = $viewerValidationLevel <= $adjustedValidationLevel;

        if (!$validationPassed) {
            // View failed validation - still record it but mark as failed
            $this->recordFailedView(
                $video->id,
                $ipAddress,
                $currentCpm,
                $viaResult,
                $originalValidationLevel,
                $adjustedValidationLevel,
                $viewerValidationLevel
            );
            return $mockResponse;
        }

        // 5. View passed validation - record with income information
        $incomeAmount = $currentCpm; // 1 view = CPM amount

        DB::transaction(
            function () use ($owner, $video, $ipAddress, $incomeAmount, $currentCpm, $viaResult, $originalValidationLevel, $viewerValidationLevel, $adjustedValidationLevel) {
                View::create([
                    'video_id' => $video->id,
                    'ip_address' => $ipAddress,
                    'income_amount' => $incomeAmount,
                    'cpm_at_time' => $currentCpm,
                    'validation_passed' => true,
                    'income_generated' => true,
                    'via' => $viaResult,
                    'vl_at_time' => $originalValidationLevel,
                    'adjusted_vl' => $adjustedValidationLevel,
                    'viewer_vl' => $viewerValidationLevel,
                ]);

                // 6. Update user balance
                $owner->balance = ($owner->balance ?? 0) + $incomeAmount;
                $owner->save();
            }
        );

        return $mockResponse;
    }

    // API endpoint untuk report video
    public function reportVideo(Request $request)
    {
        $validated = $request->validate([
            'video_code' => 'required|exists:videos,video_code',
            'description' => 'nullable|string|max:1000',
        ]);

        $video = Video::where('video_code', $validated['video_code'])->first();
        $ipAddress = $request->ip();

        // Rate limiting: Check if IP has reported this video within 6 hours
        if (VideoReport::hasRecentReport($video->id, $ipAddress)) {
            // Silent ignore - return success response
            return response()->json([
                'message' => 'Report submitted successfully.'
            ]);
        }

        // Create new report
        VideoReport::create([
            'video_id' => $video->id,
            'description' => $validated['description'],
            'reporter_ip' => $ipAddress,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Report submitted successfully.'
        ]);
    }

    private function recordFailedView(
        $videoId,
        $ipAddress,
        $currentCpm,
        $viaResult,
        $validationAtTime = null,
        $adjustedValidationLevel = null,
        $viewerValidationLevel = null
    ) {
        View::create([
            'video_id' => $videoId,
            'ip_address' => $ipAddress,
            'income_amount' => 0.00,
            'cpm_at_time' => $currentCpm,
            'validation_passed' => false,
            'income_generated' => false,
            'via' => $viaResult,
            'vl_at_time' => $validationAtTime,
            'adjusted_vl' => $adjustedValidationLevel,
            'viewer_vl' => $viewerValidationLevel,
        ]);
    }

    // Fungsi helper untuk mengambil setting
    private function getIpLimit()
    {
        return (int) (Setting::where('key', 'ip_view_limit')->first()->value ?? 2);
    }

    private function getCpm()
    {
        return (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
    }
}