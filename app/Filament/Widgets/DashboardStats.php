<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Video;
use App\Models\Withdrawal;
use App\Models\View;
use App\Models\Setting;
use App\Models\TelegramBroadcastVideo;
use App\Models\VideoReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected function getStats(): array
    {
        // Cache for 5 minutes - balance between freshness & performance
        return cache()->remember('dashboard_stats_main', 300, function() {
            // Use Eloquent aggregates for better compatibility
            $totalStoredIncome = View::where('income_generated', true)->sum('income_amount');
            $totalViews = View::count();
            $totalValidatedViews = View::where('validation_passed', true)->count();
            $totalFailedViews = View::where('validation_passed', false)->count();
            
            // Optimize withdrawal + event payout with single query
            $withdrawalStats = Withdrawal::selectRaw('
                SUM(CASE WHEN status = "confirmed" THEN amount ELSE 0 END) as total_confirmed
            ')->first();
            
            $eventPayoutStats = \App\Models\EventPayout::selectRaw('
                SUM(CASE WHEN status = "confirmed" THEN prize_amount ELSE 0 END) as total_confirmed
            ')->first();
            
            $totalWithdrawals = $withdrawalStats->total_confirmed ?? 0;
            $totalEventPayouts = $eventPayoutStats->total_confirmed ?? 0;
            $totalPaidOut = $totalWithdrawals + $totalEventPayouts;
            
            // Get current CPM
            $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
            
            // User stats in single query
            $userStats = User::selectRaw('
                COUNT(*) as total_users,
                SUM(balance) as total_balance
            ')->first();
            
            $totalUsers = $userStats->total_users ?? 0;
            $totalUserBalances = $userStats->total_balance ?? 0;
            
            // Calculate success rate
            $successRate = $totalViews > 0 ? round(($totalValidatedViews / $totalViews) * 100, 1) : 0;
            
            // Video stats using Eloquent for compatibility
            $activeVideos = Video::where('is_active', true)->count();
            $inactiveVideos = Video::where('is_active', false)->count();
            $safeVideos = Video::where('is_safe_content', true)->count();
            $unsafeVideos = Video::where('is_safe_content', false)->count();
            $todayVideos = Video::whereDate('created_at', today())->count();
            $totalVideos = Video::count();
            
            // Withdrawal stats - single query
            $withdrawalAllStats = Withdrawal::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected
            ')->first();
            
            $totalWithdrawalsCount = $withdrawalAllStats->total ?? 0;
            $confirmedWithdrawals = $withdrawalAllStats->confirmed ?? 0;
            $pendingWithdrawals = $withdrawalAllStats->pending ?? 0;
            $rejectedWithdrawals = $withdrawalAllStats->rejected ?? 0;
            
            // Pending reports count
            $pendingReportsCount = VideoReport::where('status', 'pending')->count();
            
            // Ready to broadcast count
            $readyToBroadcast = Video::whereDoesntHave('telegramBroadcast')
                ->where('is_active', true)
                ->where('is_safe_content', true)
                ->whereNotNull('thumbnail_path')
                ->count();
            
            // Broadcasted count
            $broadcastedCount = TelegramBroadcastVideo::count();
            
            // Thumbnail stats
            $withThumbnail = Video::whereNotNull('thumbnail_path')->count();
            $withoutThumbnail = Video::whereNull('thumbnail_path')->count();

        return [
            Stat::make('💰 Total Pendapatan Platform', 'Rp' . number_format($totalStoredIncome, 0, ',', '.'))
                ->description("Dari {$totalValidatedViews} views yang lolos validasi")
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('💸 Sudah Dibayar', 'Rp' . number_format($totalPaidOut, 0, ',', '.'))
                ->description("Withdrawal + Event Payouts")
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
            Stat::make('💳 Saldo User', 'Rp' . number_format($totalUserBalances, 0, ',', '.'))
                ->description("Belum ditarik oleh user")
                ->icon('heroicon-o-wallet')
                ->color('warning'),
            Stat::make('📊 Total Views & Validasi', number_format($totalViews))
                ->description("Lolos: {$totalValidatedViews} | Gagal: {$totalFailedViews} | Rate: {$successRate}%")
                ->icon('heroicon-o-eye')
                ->color('info'),
            Stat::make('⚙️ CPM Saat Ini', 'Rp' . number_format($currentCpm, 0, ',', '.'))
                ->description("Pengaturan CPM terkini")
                ->icon('heroicon-o-cog-6-tooth')
                ->color('warning'),
            Stat::make('👥 Total Pengguna', $totalUsers)
                ->description('User terdaftar')
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('🎥 Video Aktif', $activeVideos)
                ->description('Video yang tersedia')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('⏸️ Video Tidak Aktif', $inactiveVideos)
                ->description('Video yang dinonaktifkan')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
            Stat::make('📅 Video Hari Ini', $todayVideos)
                ->description('Video yang ditambahkan hari ini')
                ->icon('heroicon-o-calendar-days')
                ->color('primary'),
            
            // Telegram Broadcast Stats (combined)
            Stat::make('📢 Telegram Broadcast', $broadcastedCount + $readyToBroadcast)
                ->description("Broadcasted: {$broadcastedCount} | Ready: {$readyToBroadcast}" . ($readyToBroadcast === 0 ? ' ⚠️ No videos ready!' : ''))
                ->icon($readyToBroadcast === 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-paper-airplane')
                ->color($readyToBroadcast === 0 ? 'danger' : 'info'),
            
            // Withdrawal stats
            Stat::make('📋 Withdrawal Status', $totalWithdrawalsCount)
                ->description("Berhasil: {$confirmedWithdrawals} | Pending: {$pendingWithdrawals} | Ditolak: {$rejectedWithdrawals}")
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning'),
            
            Stat::make('⚠️ Pending Reports', $pendingReportsCount)
                ->description('Video reports yang perlu direview')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            
            Stat::make('🛡️ Safe Content Rate', $totalVideos > 0 ? round(($safeVideos / $totalVideos) * 100, 1) . '%' : '0%')
                ->description($safeVideos . ' Safe | ' . $unsafeVideos . ' Unsafe')
                ->icon('heroicon-o-shield-check')
                ->color('success'),
            
            Stat::make('💰 Revenue Per User', $totalUsers > 0 ? 'Rp' . number_format($totalStoredIncome / $totalUsers, 0, ',', '.') : 'Rp0')
                ->description('Average revenue per user')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),
            
            Stat::make('📷 Video Thumbnails', $withThumbnail)
                ->description("With: {$withThumbnail} | Without: {$withoutThumbnail}")
                ->icon($withoutThumbnail > $withThumbnail ? 'heroicon-o-exclamation-circle' : 'heroicon-o-photo')
                ->color($withoutThumbnail > $withThumbnail ? 'warning' : 'success'),
        ];
        });
    }
}