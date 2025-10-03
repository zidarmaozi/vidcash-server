<?php

use App\Console\Commands\CheckVideoAvailability;
use App\Console\Commands\CleanupOldBroadcasts;
use App\Console\Commands\SendVideoToTelegram;
use App\Console\Commands\UpdateVideoThumbnail;
use App\Console\Commands\SendAdvertiseToTelegram;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\AwardMonthlyPrizes;
use App\Console\Commands\PruneReadNotifications;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 3. Tambahkan jadwal di sini
Schedule::command(AwardMonthlyPrizes::class)->monthlyOn(1, '01:00');

//notification
// Hapus notifikasi yang sudah dibaca dan lebih tua dari 7 hari
// Jadwalkan perintah ini untuk dijalankan setiap hari
// Jalankan setiap hari pada tengah malam
Schedule::command(PruneReadNotifications::class)->daily();

// Video active worker
Schedule::command(CheckVideoAvailability::class)->everyTenMinutes();

// Video thumbnail worker
Schedule::command(UpdateVideoThumbnail::class)->everyFiveMinutes();

// video sender
Schedule::command(SendVideoToTelegram::class)->at('01:00');
Schedule::command(SendVideoToTelegram::class)->at('05:00');
Schedule::command(SendVideoToTelegram::class)->at('12:45');

// cleanup old broadcasts
Schedule::command(CleanupOldBroadcasts::class)->daily();

// send advertise to telegram (every 2 days at 09:00 Asia/Jakarta time)
Schedule::command(SendAdvertiseToTelegram::class)
    ->days(2)
    ->at('09:00');