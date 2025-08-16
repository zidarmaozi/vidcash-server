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
        
        // Calculate total platform income from all views
        $totalViews = View::count();
        $cpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
        $totalPlatformIncome = $totalViews * $cpm;
        
        // Calculate current user balances
        $totalUserBalances = User::sum('balance');
        
        // Calculate total paid out (withdrawals + event payouts)
        $totalPaidOut = $totalWithdrawals + User::sum('total_withdrawn');

        return [
            Stat::make('💰 TOTAL PENDAPATAN PLATFORM', 'Rp' . number_format($totalPlatformIncome, 0, ',', '.'))
                ->description("Dari {$totalViews} views × Rp{$cpm} CPM")
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Total Pengguna', User::count())
                ->icon('heroicon-o-users'),
            Stat::make('Total Video', Video::count())
                ->icon('heroicon-o-video-camera'),
            Stat::make('Total Views', number_format($totalViews))
                ->description('Semua views yang telah direkam')
                ->icon('heroicon-o-eye')
                ->color('info'),
            Stat::make('Saldo User Saat Ini', 'Rp' . number_format($totalUserBalances, 0, ',', '.'))
                ->description('Total saldo yang belum ditarik')
                ->icon('heroicon-o-wallet')
                ->color('warning'),
            Stat::make('Total Sudah Dibayar', 'Rp' . number_format($totalPaidOut, 0, ',', '.'))
                ->description('Total yang sudah ditarik + event payouts')
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
            Stat::make('Penarikan Berhasil', Withdrawal::where('status', 'confirmed')->count()),
            Stat::make('Penarikan Pending', Withdrawal::where('status', 'pending')->count()),
            Stat::make('Penarikan Ditolak', Withdrawal::where('status', 'rejected')->count()),
        ];
    }
}