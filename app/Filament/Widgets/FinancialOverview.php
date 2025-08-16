<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverview extends BaseWidget
{
    protected ?string $heading = 'Ringkasan Keuangan Platform';

    protected function getStats(): array
    {
        // Get CPM setting
        $cpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
        
        // Calculate total platform income
        $totalViews = View::count();
        $totalPlatformIncome = $totalViews * $cpm;
        
        // Calculate current user balances
        $totalUserBalances = User::sum('balance');
        
        // Calculate total paid out
        $totalWithdrawals = Withdrawal::where('status', 'confirmed')->sum('amount');
        $totalPaidOut = $totalWithdrawals + User::sum('total_withdrawn');
        
        // Calculate pending amounts
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->sum('amount');
        
        // Calculate net platform profit (if any)
        $netPlatformProfit = $totalPlatformIncome - $totalPaidOut - $totalUserBalances;

        return [
            Stat::make('Total Pendapatan Platform', 'Rp' . number_format($totalPlatformIncome, 0, ',', '.'))
                ->description("Dari {$totalViews} views × Rp{$cpm} CPM")
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            
            Stat::make('Total Sudah Dibayar', 'Rp' . number_format($totalPaidOut, 0, ',', '.'))
                ->description('Penarikan + Event Payouts')
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
            
            Stat::make('Saldo User Saat Ini', 'Rp' . number_format($totalUserBalances, 0, ',', '.'))
                ->description('Belum ditarik')
                ->icon('heroicon-o-wallet')
                ->color('warning'),
            
            Stat::make('Penarikan Pending', 'Rp' . number_format($pendingWithdrawals, 0, ',', '.'))
                ->description('Menunggu konfirmasi')
                ->icon('heroicon-o-clock')
                ->color('info'),
            
            Stat::make('Net Platform Profit', 'Rp' . number_format($netPlatformProfit, 0, ',', '.'))
                ->description('Pendapatan - Dibayar - Saldo User')
                ->icon('heroicon-o-chart-bar')
                ->color($netPlatformProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}
