<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use App\Filament\Widgets\FilteredDashboardStats;
use App\Filament\Widgets\FilteredFinancialOverview;
use App\Filament\Widgets\FilteredMonthlyStatsChart;
use App\Filament\Widgets\PendingWithdrawals;

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

        // Register Observers
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        // Register Livewire Components for Filtered Dashboard
        Livewire::component('app.filament.widgets.filtered-dashboard-stats', FilteredDashboardStats::class);
        Livewire::component('app.filament.widgets.filtered-financial-overview', FilteredFinancialOverview::class);
        Livewire::component('app.filament.widgets.filtered-monthly-stats-chart', FilteredMonthlyStatsChart::class);
        Livewire::component('app.filament.widgets.pending-withdrawals', PendingWithdrawals::class);

        // New Widgets
        Livewire::component('app.filament.widgets.filtered-viewer-source-chart', \App\Filament\Widgets\FilteredViewerSourceChart::class);
        Livewire::component('app.filament.widgets.top-referrers-widget', \App\Filament\Widgets\TopReferrersWidget::class);
        Livewire::component('app.filament.widgets.top-performing-videos-widget', \App\Filament\Widgets\TopPerformingVideosWidget::class);
    }
}
