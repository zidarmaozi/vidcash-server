<?php

use App\Console\Commands\CheckVideoAvailability;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <-- 1. Impor Schedule
use App\Console\Commands\AwardMonthlyPrizes; // <-- 2. Impor Command Anda
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