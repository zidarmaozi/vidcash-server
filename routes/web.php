<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\PaymentAccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Api\ServiceController;

// Rute ini untuk landing page dan bisa diakses publik
Route::get('/', function () {
    // Jika user sudah login, arahkan ke dashboard, jika belum, tampilkan landing page
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Grup ini sekarang melindungi SEMUA halaman khusus user
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Dashboard SEKARANG ADA DI DALAM
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Video & Folder
    // Rute Video & Folder
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/generate', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::patch('/videos/{video}', [VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::post('/videos/bulk-action', [VideoController::class, 'bulkAction'])->name('videos.bulkAction');
    Route::post('/videos/generate-from-links', [VideoController::class, 'generateFromLinks'])->name('videos.generateFromLinks');

    // Folder Routes
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::patch('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
    Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');

    // Rute Penarikan & Akun Pembayaran
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::post('/payment-accounts', [PaymentAccountController::class, 'store'])->name('payment-accounts.store');
    Route::delete('/payment-accounts/{account}', [PaymentAccountController::class, 'destroy'])->name('payment-accounts.destroy');

    // Rute Referral/Undang Teman
    Route::get('/referral', [\App\Http\Controllers\ReferralController::class, 'index'])->name('referral.index');

});



// Rute untuk save-link (tetap di luar grup role:user agar bisa diakses oleh user yang login)
// Rute untuk save-link (tetap di luar grup role:user agar bisa diakses oleh user yang login)
Route::post('/videos/save-link', [VideoController::class, 'saveLinkFromApi'])->middleware('auth')->name('videos.saveLink');

// Public Folder Route
Route::get('/f/{slug}', [FolderController::class, 'showPublic'])->name('folders.public');

// leaderboard
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

// API Routes untuk service eksternal
Route::post('/api/report-video', [ServiceController::class, 'reportVideo'])->name('api.report-video');

require __DIR__ . '/auth.php';
