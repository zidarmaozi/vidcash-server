<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Video;
use App\Models\View;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodayHighlights extends BaseWidget
{
    protected static ?int $sort = 0;
    
    protected function getHeading(): ?string
    {
        return '📅 Today\'s Highlights';
    }

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Today's data
        $todayUsers = User::whereDate('created_at', $today)->count();
        $todayVideos = Video::whereDate('created_at', $today)->count();
        $todayViews = View::whereDate('created_at', $today)->count();
        $todayRevenue = View::whereDate('created_at', $today)
            ->where('income_generated', true)
            ->sum('income_amount');

        // Yesterday's data for comparison
        $yesterdayUsers = User::whereDate('created_at', $yesterday)->count();
        $yesterdayVideos = Video::whereDate('created_at', $yesterday)->count();
        $yesterdayViews = View::whereDate('created_at', $yesterday)->count();
        $yesterdayRevenue = View::whereDate('created_at', $yesterday)
            ->where('income_generated', true)
            ->sum('income_amount');

        // Calculate growth percentages
        $userGrowth = $yesterdayUsers > 0 
            ? round((($todayUsers - $yesterdayUsers) / $yesterdayUsers) * 100, 1)
            : ($todayUsers > 0 ? 100 : 0);

        $videoGrowth = $yesterdayVideos > 0 
            ? round((($todayVideos - $yesterdayVideos) / $yesterdayVideos) * 100, 1)
            : ($todayVideos > 0 ? 100 : 0);

        $viewGrowth = $yesterdayViews > 0 
            ? round((($todayViews - $yesterdayViews) / $yesterdayViews) * 100, 1)
            : ($todayViews > 0 ? 100 : 0);

        $revenueGrowth = $yesterdayRevenue > 0 
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : ($todayRevenue > 0 ? 100 : 0);

        return [
            Stat::make('👥 New Users Today', $todayUsers)
                ->description(($userGrowth >= 0 ? '+' : '') . $userGrowth . '% vs yesterday')
                ->descriptionIcon($userGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($userGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('🎥 New Videos Today', $todayVideos)
                ->description(($videoGrowth >= 0 ? '+' : '') . $videoGrowth . '% vs yesterday')
                ->descriptionIcon($videoGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($videoGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('👁️ Views Today', number_format($todayViews))
                ->description(($viewGrowth >= 0 ? '+' : '') . $viewGrowth . '% vs yesterday')
                ->descriptionIcon($viewGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($viewGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('💰 Revenue Today', 'Rp' . number_format($todayRevenue, 0, ',', '.'))
                ->description(($revenueGrowth >= 0 ? '+' : '') . $revenueGrowth . '% vs yesterday')
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger'),
        ];
    }
}

