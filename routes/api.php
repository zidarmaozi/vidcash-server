<?php

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;

Route::get('/now', function () {
    return response()->json([
        'message' => 'Hello World',
        'time' => now()->toDateTimeString(),
    ]);
});

Route::get('/service/settings/{videoCode?}', [ServiceController::class, 'getSettings']);
Route::get('/service/related-videos/{videoCode?}', [ServiceController::class, 'getRelatedVideos']);
Route::get('/service/folder-videos/{folderSlug}', [ServiceController::class, 'getFolderVideos']);
Route::post('/service/record-view', [ServiceController::class, 'recordView']);
Route::get('/video-info/{video:video_code}', [ServiceController::class, 'getVideoInfo']);

Route::get('thumbnail-check', function () {
    $video = Video::whereNotNull('thumbnail_path')
        ->where('is_safe_content', '!=', true)
        ->where('is_ai_checked', '!=', true)
        ->orderBy('created_at', 'desc')
        ->first();

    return response()->json([
        'is_available' => $video ? true : false,
        'video_code' => $video?->video_code,
    ]);
});

Route::post('thumbnail-check', function (Request $request) {
    $validated = $request->validate([
        'video_code' => 'required|exists:videos,video_code',
        'is_safe_content' => 'required|boolean',
    ]);

    $video = Video::where('video_code', $validated['video_code'])->first();

    if (!$video) {
        return response()->json([
            'message' => 'No video found',
        ], 404);
    }

    $video->is_safe_content = $validated['is_safe_content'];
    $video->is_ai_checked = true;
    $video->save();

    return response()->json([
        'message' => 'Video checked successfully',
    ], 200);
});