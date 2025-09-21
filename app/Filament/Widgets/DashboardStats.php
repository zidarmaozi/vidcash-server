<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Video;
use App\Models\Withdrawal;
use App\Models\View;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
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

        return [
            Stat::make('💰 Total Pendapatan Platform', 'Rp' . number_format($totalStoredIncome, 0, ',', '.'))
                ->description("Dari {$totalValidatedViews} views yang lolos validasi")
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('📊 Total Views & Validasi', number_format($totalViews))
                ->description("Lolos: {$totalValidatedViews} | Gagal: {$totalFailedViews}")
                ->icon('heroicon-o-eye')
                ->color('info'),
            Stat::make('⚙️ CPM Saat Ini', 'Rp' . number_format($currentCpm, 0, ',', '.'))
                ->description("Pengaturan CPM terkini")
                ->icon('heroicon-o-cog-6-tooth')
                ->color('warning'),
            Stat::make('Total Pengguna', User::count())
                ->icon('heroicon-o-users'),
            Stat::make('Total Video', Video::count())
                ->description(Video::where('is_active', true)->count() . ' Aktif | ' . Video::where('is_active', false)->count() . ' Tidak Aktif')
                ->icon('heroicon-o-video-camera')
                ->color('info'),
            Stat::make('Video Aktif', Video::where('is_active', true)->count())
                ->description('Video yang tersedia dan dapat diakses')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Video Tidak Aktif', Video::where('is_active', false)->count())
                ->description('Video yang dinonaktifkan atau tidak tersedia')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
            Stat::make('Video Hari Ini', Video::whereDate('created_at', today())->count())
                ->description('Video yang ditambahkan hari ini')
                ->icon('heroicon-o-calendar-days')
                ->color('primary'),
            Stat::make('Video Minggu Ini', Video::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count())
                ->description('Video yang ditambahkan minggu ini')
                ->icon('heroicon-o-calendar')
                ->color('primary'),
            Stat::make('Video dengan Views', Video::has('views')->count())
                ->description('Video yang sudah pernah dilihat')
                ->icon('heroicon-o-eye')
                ->color('success'),
            Stat::make('Video Tanpa Views', Video::doesntHave('views')->count())
                ->description('Video yang belum pernah dilihat')
                ->icon('heroicon-o-eye-slash')
                ->color('gray'),
            Stat::make('💸 Financial Overview', 'Rp' . number_format($totalStoredIncome, 0, ',', '.'))
                ->description("Pendapatan: Rp" . number_format($totalStoredIncome, 0, ',', '.') . " | Dibayar: Rp" . number_format($totalPaidOut, 0, ',', '.') . " | Saldo: Rp" . number_format($totalUserBalances, 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->url(route('filament.admin.pages.income-report')),
            Stat::make('📋 Withdrawal Status', Withdrawal::count())
                ->description("Berhasil: " . Withdrawal::where('status', 'confirmed')->count() . " | Pending: " . Withdrawal::where('status', 'pending')->count() . " | Ditolak: " . Withdrawal::where('status', 'rejected')->count())
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning'),
        ];
    }
}