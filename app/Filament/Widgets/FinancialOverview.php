<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\EventPayout;
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
        
        // Calculate total platform income from ALL video views
        $totalViews = View::count();
        $totalPlatformIncome = $totalViews * $cpm;
        
        // Calculate current user balances
        $totalUserBalances = User::sum('balance');
        
        // Calculate total paid out (withdrawals + event payouts)
        $totalWithdrawals = Withdrawal::where('status', 'confirmed')->sum('amount');
        $totalEventPayouts = EventPayout::where('status', 'confirmed')->sum('prize_amount');
        $totalPaidOut = $totalWithdrawals + $totalEventPayouts + User::sum('total_withdrawn');
        
        // Calculate pending amounts
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->sum('amount');
        $pendingEventPayouts = EventPayout::where('status', 'pending')->sum('prize_amount');
        
        // Calculate net platform profit (if any)
        $netPlatformProfit = $totalPlatformIncome - $totalPaidOut - $totalUserBalances;

        return [
            Stat::make('💰 TOTAL PENDAPATAN PLATFORM', 'Rp' . number_format($totalPlatformIncome, 0, ',', '.'))
                ->description("Dari {$totalViews} views × Rp{$cpm} CPM - INI ADALAH TOTAL INCOME APLIKASI")
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            
            Stat::make('Total Sudah Dibayar', 'Rp' . number_format($totalPaidOut, 0, ',', '.'))
                ->description("Penarikan: Rp" . number_format($totalWithdrawals, 0, ',', '.') . " + Event: Rp" . number_format($totalEventPayouts, 0, ',', '.') . " + Total Withdrawn: Rp" . number_format(User::sum('total_withdrawn'), 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
            
            Stat::make('Saldo User Saat Ini', 'Rp' . number_format($totalUserBalances, 0, ',', '.'))
                ->description('Belum ditarik')
                ->icon('heroicon-o-wallet')
                ->color('warning'),
            
            Stat::make('Pending (Withdrawal + Event)', 'Rp' . number_format($pendingWithdrawals + $pendingEventPayouts, 0, ',', '.'))
                ->description("Penarikan: Rp" . number_format($pendingWithdrawals, 0, ',', '.') . " + Event: Rp" . number_format($pendingEventPayouts, 0, ',', '.'))
                ->icon('heroicon-o-clock')
                ->color('info'),
            
            Stat::make('Net Platform Profit', 'Rp' . number_format($netPlatformProfit, 0, ',', '.'))
                ->description('Pendapatan - Dibayar - Saldo User')
                ->icon('heroicon-o-chart-bar')
                ->color($netPlatformProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}
