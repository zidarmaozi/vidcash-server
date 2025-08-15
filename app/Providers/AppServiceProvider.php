<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    // Bagikan data notifikasi ke semua view yang menggunakan layout 'layouts.app'
    View::composer('layouts.app', function ($view) {
       if (Auth::check()) {
    // Gunakan relasi baru kita
    $notifications = Auth::user()->customNotifications()->latest()->take(5)->get();
    $unreadCount = Auth::user()->customNotifications()->whereNull('read_at')->count();
    $view->with('userNotifications', $notifications)->with('unreadNotificationsCount', $unreadCount);
}
    });
}
}
