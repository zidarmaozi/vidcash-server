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
        // Get current CPM setting for comparison
        $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
        
        // Calculate total platform income from STORED income amounts (not dynamic calculation)
        $totalStoredIncome = View::where('income_generated', true)->sum('income_amount');
        $totalViews = View::count();
        $totalValidatedViews = View::where('validation_passed', true)->count();
        $totalFailedViews = View::where('validation_passed', false)->count();
        
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
        $netPlatformProfit = $totalStoredIncome - $totalPaidOut - $totalUserBalances;

        return [
            Stat::make('💰 TOTAL PENDAPATAN PLATFORM (STORED)', 'Rp' . number_format($totalStoredIncome, 0, ',', '.'))
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
