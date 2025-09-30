<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;

Route::get('/service/settings/{videoCode?}', [ServiceController::class, 'getSettings']);
Route::get('/service/related-videos', [ServiceController::class, 'getRelatedVideos']);
Route::post('/service/record-view', [ServiceController::class, 'recordView']);
Route::get('/video-info/{video:video_code}', [ServiceController::class, 'getVideoInfo']);