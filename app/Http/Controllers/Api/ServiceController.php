<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Video;
use App\Models\View;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Mengirim pengaturan ke service javascript/halam tonton
    public function getSettings()
    {
        $ipLimitSetting = Setting::where('key', 'ip_view_limit')->first();
        $cpmSetting = Setting::where('key', 'cpm')->first();
        $watchTimeSetting = Setting::where('key', 'watch_time_seconds')->first();
        // Ambil data level validasi default
        $validationLevelSetting = Setting::where('key', 'default_validation_level')->first();

        return response()->json([
            'ip_view_limit' => $ipLimitSetting ? (int) $ipLimitSetting->value : 2,
            'cpm' => $cpmSetting ? (int) $cpmSetting->value : 10,
            'watch_time_seconds' => $watchTimeSetting ? (int) $watchTimeSetting->value : 10,
            // TAMBAHKAN KEY INI
            'default_validation_level' => $validationLevelSetting ? (int) $validationLevelSetting->value : 5
        ]);
    }

    // Menerima perintah untuk mencatat view dari service Node.js
    public function recordView(Request $request)
    {
        $validated = $request->validate([
            'video_code' => 'required|exists:videos,video_code',
        ]);

        $video = Video::where('video_code', $validated['video_code'])->first();
        $owner = $video->user;
        $ipAddress = $request->ip();

        // 1. Validasi Batas IP
        $ipLimit = $this->getIpLimit();
        $existingViews = View::where('video_id', $video->id)
            ->where('ip_address', $ipAddress)->count();

        if ($existingViews >= $ipLimit) {
            return response()->json(['message' => 'View limit reached.'], 429);
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
        
        if (!$validationPassed) {
            // View failed validation - still record it but mark as failed
            View::create([
                'video_id' => $video->id,
                'ip_address' => $ipAddress,
                'income_amount' => 0.00,
                'cpm_at_time' => $currentCpm,
                'validation_passed' => false,
                'income_generated' => false,
            ]);

            return response()->json([
                'message' => 'View not validated.',
                'debug' => [
                    'randomNumber' => $randomNumber,
                    'validationLevel' => $validationLevel
                ]
            ], 422);
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
        ]);

        // 6. Update user balance
        $owner->balance += $incomeAmount;
        $owner->save();

        return response()->json([
            'message' => 'View recorded successfully.',
            'income_generated' => $incomeAmount,
            'cpm_used' => $currentCpm
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