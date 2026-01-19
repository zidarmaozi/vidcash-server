<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Video;
use App\Models\Withdrawal;
use App\Models\View;
use App\Models\Setting;
use App\Models\TelegramBroadcastVideo;
use App\Models\VideoReport;
use App\Services\TelegramService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        // Cache for 5 minutes
        return cache()->remember('dashboard_stats_main', 300, function () {
            // Financials (Aggregated)
            $totalStoredIncome = View::where('income_generated', true)->sum('income_amount');

            // Withdrawal & Payouts (Aggregated)
            $withdrawalConfirmed = Withdrawal::where('status', 'confirmed')->sum('amount');
            $eventPayoutConfirmed = \App\Models\EventPayout::where('status', 'confirmed')->sum('prize_amount');
            $totalPaidOut = $withdrawalConfirmed + $eventPayoutConfirmed;

            // User Balances
            $totalUserBalances = User::sum('balance');

            // Platform Totals
            $totalUsers = User::count();
            $totalViews = View::count();
            $activeVideos = Video::where('is_active', true)->count();
            $totalVideos = Video::count();

            // Safe Content Rate
            $safeVideos = Video::where('is_safe_content', true)->count();
            $safeRate = $totalVideos > 0 ? round(($safeVideos / $totalVideos) * 100, 1) : 0;

            // Current CPM
            $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);

            return [
                Stat::make('💰 Total Revenue', 'Rp' . number_format($totalStoredIncome, 0, ',', '.'))
                    ->description('Total pendapatan iklan platform')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success'),

                Stat::make('💸 Total Masuk Rekening', 'Rp' . number_format($totalPaidOut, 0, ',', '.'))
                    ->description('Total uang yang sudah dibayarkan (WD + Event)')
                    ->icon('heroicon-o-banknotes')
                    ->color('danger'),

                Stat::make('💳 Total Saldo Mengendap', 'Rp' . number_format($totalUserBalances, 0, ',', '.'))
                    ->description('Kewajiban bayar ke user')
                    ->icon('heroicon-o-wallet')
                    ->color('warning'),

                Stat::make('👥 Total Users', number_format($totalUsers))
                    ->description('User terdaftar')
                    ->icon('heroicon-o-users')
                    ->color('primary'),

                Stat::make('🎥 Total Videos', number_format($totalVideos))
                    ->description($activeVideos . ' Active | ' . ($totalVideos - $activeVideos) . ' Inactive')
                    ->icon('heroicon-o-video-camera')
                    ->color('info'),

                Stat::make('👁️ Total Views', number_format($totalViews))
                    ->description('All time views')
                    ->icon('heroicon-o-eye')
                    ->color('gray'),

                Stat::make('🛡️ Safe Content Rate', $safeRate . '%')
                    ->description('Persentase video aman')
                    ->icon('heroicon-o-shield-check')
                    ->color($safeRate > 90 ? 'success' : 'warning'),

                Stat::make('⚙️ CPM Saat Ini', 'Rp' . number_format($currentCpm, 0, ',', '.'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('gray'),
            ];
        });
    }
}