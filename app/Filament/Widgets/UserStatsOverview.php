<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class UserStatsOverview extends BaseWidget
{
    public $record;

    public function mount($record = null): void
    {
        $this->record = $record;
    }

    public function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $user = $this->record;
        
        // Calculate financial stats
        $totalIncome = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->where('videos.user_id', $user->id)
            ->where('views.income_generated', true)
            ->sum('views.income_amount');

        $pendingWithdrawals = DB::table('withdrawals')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $pendingEventPayouts = DB::table('event_payouts')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('prize_amount');

        // Calculate video performance stats
        $totalViews = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->where('videos.user_id', $user->id)
            ->where('views.validation_passed', true)
            ->count();

        $totalViewsAll = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->where('videos.user_id', $user->id)
            ->count();

        $successRate = $totalViewsAll > 0 ? round(($totalViews / $totalViewsAll) * 100, 1) : 0;

        return [
            Stat::make('Current Balance', 'Rp' . number_format($user->balance, 0, ',', '.'))
                ->description('Available balance for withdrawal')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Total Income Generated', 'Rp' . number_format($totalIncome, 0, ',', '.'))
                ->description('All-time earnings from videos')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('Total Withdrawn', 'Rp' . number_format($user->total_withdrawn, 0, ',', '.'))
                ->description('Amount already withdrawn')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),

            Stat::make('Pending Amounts', 'Rp' . number_format($pendingWithdrawals + $pendingEventPayouts, 0, ',', '.'))
                ->description('Withdrawals + Event payouts pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Videos', $user->videos()->count())
                ->description('Videos uploaded')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('primary'),

            Stat::make('Total Views', number_format($totalViews))
                ->description("Success rate: {$successRate}%")
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),
        ];
    }
}
