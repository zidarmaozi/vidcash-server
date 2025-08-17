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
            Stat::make('💰 TOTAL PENDAPATAN APLIKASI (STORED)', 'Rp' . number_format($totalStoredIncome, 0, ',', '.'))
                ->description("Dari {$totalValidatedViews} views yang lolos validasi - INI ADALAH TOTAL INCOME KESELURUHAN")
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
                ->icon('heroicon-o-video-camera'),
            Stat::make('Saldo User Saat Ini', 'Rp' . number_format($totalUserBalances, 0, ',', '.'))
                ->description('Total saldo yang belum ditarik')
                ->icon('heroicon-o-wallet')
                ->color('warning'),
            Stat::make('Total Sudah Dibayar', 'Rp' . number_format($totalPaidOut, 0, ',', '.'))
                ->description("Penarikan: Rp" . number_format($totalWithdrawals, 0, ',', '.') . " + Event: Rp" . number_format($totalEventPayouts, 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
            Stat::make('Penarikan Berhasil', Withdrawal::where('status', 'confirmed')->count()),
            Stat::make('Penarikan Pending', Withdrawal::where('status', 'pending')->count()),
            Stat::make('Penarikan Ditolak', Withdrawal::where('status', 'rejected')->count()),
        ];
    }
}