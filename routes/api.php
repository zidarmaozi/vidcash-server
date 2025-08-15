<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;

Route::get('/service/settings', [ServiceController::class, 'getSettings']);
Route::post('/service/record-view', [ServiceController::class, 'recordView']);
Route::get('/video-info/{video:video_code}', [ServiceController::class, 'getVideoInfo']);