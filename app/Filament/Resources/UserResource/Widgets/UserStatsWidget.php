<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserStatsWidget extends BaseWidget
{
    public ?User $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        // Calculate financial stats
        $totalIncome = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->where('videos.user_id', $this->record->id)
            ->where('views.income_generated', true)
            ->sum('views.income_amount') ?? 0;

        $pendingWithdrawals = DB::table('withdrawals')
            ->where('user_id', $this->record->id)
            ->where('status', 'pending')
            ->sum('amount') ?? 0;

        $pendingEventPayouts = DB::table('event_payouts')
            ->where('user_id', $this->record->id)
            ->where('status', 'pending')
            ->sum('prize_amount') ?? 0;

        // Calculate video performance stats
        $totalViews = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->where('videos.user_id', $this->record->id)
            ->where('views.validation_passed', true)
            ->count();

        $totalViewsAll = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->where('videos.user_id', $this->record->id)
            ->count();

        $successRate = $totalViewsAll > 0 ? round(($totalViews / $totalViewsAll) * 100, 1) : 0;

        // Video stats
        $activeVideos = $this->record->videos()->where('is_active', true)->count();
        $totalVideos = $this->record->videos()->count();

        return [
            Stat::make('💰 Saldo', 'Rp' . number_format((float) $this->record->balance, 0, ',', '.'))
                ->description('Saldo tersedia untuk penarikan')
                ->color('success'),

            Stat::make('📊 Total Pendapatan', 'Rp' . number_format((float) $totalIncome, 0, ',', '.'))
                ->description('Total earnings dari video')
                ->color('info'),

            Stat::make('💸 Total Ditarik', 'Rp' . number_format((float) $this->record->total_withdrawn, 0, ',', '.'))
                ->description('Jumlah yang sudah ditarik')
                ->color('danger'),

            Stat::make('⏳ Pending', 'Rp' . number_format($pendingWithdrawals + $pendingEventPayouts, 0, ',', '.'))
                ->description('Withdrawal + Event payouts pending')
                ->color('warning'),

            Stat::make('🎥 Video', number_format($totalVideos))
                ->description("Active: {$activeVideos} | Inactive: " . ($totalVideos - $activeVideos))
                ->color('primary'),

            Stat::make('👁️ Views', number_format($totalViews))
                ->description("Success rate: {$successRate}%")
                ->color('success'),
        ];
    }
}

