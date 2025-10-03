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
        $totalWithdrawals = Withdrawal::where('status', 'confirmed')->sum('amount');
        $totalEventPayouts = \App\Models\EventPayout::where('status', 'confirmed')->sum('prize_amount');
        
        // Calculate total platform income from STORED income amounts (not dynamic calculation)
        $totalStoredIncome = View::where('income_generated', true)->sum('income_amount');
        $totalViews = View::count();
        $totalValidatedViews = View::where('validation_passed', true)->count();
        $totalFailedViews = View::where('validation_passed', false)->count();
        
        // Get current CPM for comparison
        $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
        
        // Calculate current user balances
        $totalUserBalances = User::sum('balance');
        
        // Calculate total paid out (withdrawals + event payouts)
        $totalPaidOut = $totalWithdrawals + $totalEventPayouts;

        // Calculate validation success rate
        $successRate = $totalViews > 0 ? round(($totalValidatedViews / $totalViews) * 100, 1) : 0;
        
        // Calculate active/inactive videos
        $activeVideos = Video::where('is_active', true)->count();
        $inactiveVideos = Video::where('is_active', false)->count();

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
            Stat::make('👥 Total Pengguna', User::count())
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
            Stat::make('📅 Video Hari Ini', Video::whereDate('created_at', today())->count())
                ->description('Video yang ditambahkan hari ini')
                ->icon('heroicon-o-calendar-days')
                ->color('primary'),
            Stat::make('📢 Video Sudah Di-broadcast', TelegramBroadcastVideo::count())
                ->description('Video yang sudah dikirim ke Telegram')
                ->icon('heroicon-o-paper-airplane')
                ->color('info'),
            Stat::make('📭 Video Ready to Broadcast', Video::whereDoesntHave('telegramBroadcast')
                ->where('is_active', true)
                ->where('is_safe_content', true)
                ->whereNotNull('thumbnail_path')
                ->count())
                ->description('Safe content yang belum di-broadcast')
                ->icon('heroicon-o-inbox')
                ->color('warning'),
            Stat::make('📋 Withdrawal Status', Withdrawal::count())
                ->description("Berhasil: " . Withdrawal::where('status', 'confirmed')->count() . " | Pending: " . Withdrawal::where('status', 'pending')->count() . " | Ditolak: " . Withdrawal::where('status', 'rejected')->count())
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning'),
            Stat::make('⚠️ Pending Reports', VideoReport::where('status', 'pending')->count())
                ->description('Video reports yang perlu direview')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            Stat::make('🛡️ Safe Content Rate', function() {
                $totalVideos = Video::count();
                if ($totalVideos === 0) return '0%';
                
                $safeVideos = Video::where('is_safe_content', true)->count();
                $safeRate = round(($safeVideos / $totalVideos) * 100, 1);
                
                return $safeRate . '%';
            })
                ->description(Video::where('is_safe_content', true)->count() . ' Safe | ' . Video::where('is_safe_content', false)->count() . ' Unsafe')
                ->icon('heroicon-o-shield-check')
                ->color('success'),
            Stat::make('💰 Revenue Per User', function() use ($totalStoredIncome) {
                $userCount = User::count();
                if ($userCount === 0) return 'Rp0';
                
                $rpu = $totalStoredIncome / $userCount;
                return 'Rp' . number_format($rpu, 0, ',', '.');
            })
                ->description('Average revenue per user')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),
        ];
    }
}