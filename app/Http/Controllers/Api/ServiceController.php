<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Video;
use App\Models\View;
use App\Models\VideoReport;
use Cache;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Mengirim pengaturan ke service javascript/halam tonton
    public function getSettings($videoCode = null)
    {
        // Cache response selama 10 menit (600 detik)
        $cacheKey = $videoCode ? "settings_{$videoCode}" : "settings_default";
        
        return Cache::remember($cacheKey, 600, function() use ($videoCode) {
            $ipLimitSetting = Setting::where('key', 'ip_view_limit')->first();
            $cpmSetting = Setting::where('key', 'cpm')->first();
            $watchTimeSetting = Setting::where('key', 'watch_time_seconds')->first();
            // Ambil data level validasi default
            $validationLevelSetting = Setting::where('key', 'default_validation_level')->first();
            $video = $videoCode ? Video::where('video_code', $videoCode)->first() : null;

            return response()->json([
                'ip_view_limit' => $ipLimitSetting ? (int) $ipLimitSetting->value : 2,
                'cpm' => $cpmSetting ? (int) $cpmSetting->value : 10,
                'watch_time_seconds' => $watchTimeSetting ? (int) $watchTimeSetting->value : 10,
                // TAMBAHKAN KEY INI
                'default_validation_level' => $validationLevelSetting ? (int) $validationLevelSetting->value : 5,
                'is_available' => (bool) $video,
                'is_active' => $video ? (bool) $video->is_active : false,
                'video_title' => $video ? $video->title : null,
            ]);
        });
    }

    public function getRelatedVideos($videoCode = null)
    {
        // make the cache into 12 hours
        $relatedVideos = Cache::remember("related_videos_{$videoCode}", 43200, function()  {
            return Video::select('id', 'video_code', 'title', 'thumbnail_path')
                ->where('is_active', true)
                ->whereNotNull('thumbnail_path')
                ->inRandomOrder()
                ->take(12)
                ->get();
        });

        return response()->json($relatedVideos);
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

        // harderning
        if ($request->header('accept-portal') !== 'x123') {
            return $mockResponse;
        }

        $video = Video::where('video_code', $validated['video_code'])->first();

        if (!$video->is_active) {
            return $mockResponse;
        }

        $owner = $video->user;
        $ipAddress = $request->ip();

        // 1. Validasi Batas IP
        $ipLimit = $this->getIpLimit();
        $existingViews = View::where('video_id', $video->id)
            ->where('ip_address', $ipAddress)->count();

        if ($existingViews >= $ipLimit) {
            // return response()->json(['message' => 'View limit reached.'], 429);
            return $mockResponse;
        }

        // 2. Tentukan Level Validasi
        if ($owner->validation_level) {
            $validationLevel = $owner->validation_level;
        } else {
            $validationLevel = (int) (Setting::where('key', 'default_validation_level')->first()->value ?? 5);
        }

        // 3. Get current CPM setting
        $currentCpm = $this->getCpm();

        // 4. Validation check
        $randomNumber = rand(1, 10);
        $validationPassed = $randomNumber <= $validationLevel;

        $via = $request->header('via');
        $viaResult = null;

        switch ($via) {
            case '1':
                $viaResult = 'direct';
                break;
            case '2':
                $viaResult = 'related';
                break;
            case '3':
                $viaResult = 'telegram';
                break;
        }
        
        if (!$validationPassed) {
            // View failed validation - still record it but mark as failed
            View::create([
                'video_id' => $video->id,
                'ip_address' => $ipAddress,
                'income_amount' => 0.00,
                'cpm_at_time' => $currentCpm,
                'validation_passed' => false,
                'income_generated' => false,
                'via' => $viaResult,
            ]);

            // return response()->json([
            //     'message' => 'View not validated.',
            //     'debug' => [
            //         'randomNumber' => $randomNumber,
            //         'validationLevel' => $validationLevel
            //     ]
            // ], 422);
            return $mockResponse;
        }
        
        // 5. View passed validation - record with income information
        $incomeAmount = $currentCpm; // 1 view = CPM amount
        
        View::create([
            'video_id' => $video->id,
            'ip_address' => $ipAddress,
            'income_amount' => $incomeAmount,
            'cpm_at_time' => $currentCpm,
            'validation_passed' => true,
            'income_generated' => true,
            'via' => $viaResult,
        ]);

        // 6. Update user balance
        $owner->balance += $incomeAmount;
        $owner->save();

        // return response()->json([
        //     'message' => 'View recorded successfully.',
        //     'income_generated' => $incomeAmount,
        //     'cpm_used' => $currentCpm
        // ]);
        return $mockResponse;
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
}