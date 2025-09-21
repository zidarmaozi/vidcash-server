<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\Withdrawal;
use App\Models\EventPayout;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SimpleIncomeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        // Get current week data
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();
        
        // Income data
        $totalIncome = View::where('income_generated', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('income_amount');
            
        $totalViews = View::whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $validatedViews = View::where('validation_passed', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $failedViews = View::where('validation_passed', false)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $avgIncomePerView = $totalViews > 0 ? $totalIncome / $totalViews : 0;
        
        // Withdrawal data
        $totalWithdrawals = Withdrawal::where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $pendingWithdrawals = Withdrawal::where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        // Event payouts
        $totalEventPayouts = EventPayout::where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('prize_amount');
        
        return [
            Stat::make('💰 Total Income (This Week)', 'Rp ' . number_format($totalIncome, 0, ',', '.'))
                ->description("From {$validatedViews} validated views")
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),
                
            Stat::make('👁️ Total Views (This Week)', number_format($totalViews))
                ->description("Valid: {$validatedViews} | Failed: {$failedViews}")
                ->color('info')
                ->icon('heroicon-o-eye'),
                
            Stat::make('✅ Validation Rate', $totalViews > 0 ? round(($validatedViews / $totalViews) * 100, 1) . '%' : '0%')
                ->description("Success rate this week")
                ->color($totalViews > 0 && ($validatedViews / $totalViews) >= 0.7 ? 'success' : 'warning')
                ->icon('heroicon-o-check-circle'),
                
            Stat::make('📊 Avg Income/View', 'Rp ' . number_format($avgIncomePerView, 2, ',', '.'))
                ->description('Average income per view')
                ->color('info')
                ->icon('heroicon-o-calculator'),
                
            Stat::make('💸 Withdrawals (This Week)', 'Rp ' . number_format($totalWithdrawals, 0, ',', '.'))
                ->description("Confirmed withdrawals")
                ->color('warning')
                ->icon('heroicon-o-banknotes'),
                
            Stat::make('⏳ Pending Withdrawals', 'Rp ' . number_format($pendingWithdrawals, 0, ',', '.'))
                ->description("Awaiting approval")
                ->color('danger')
                ->icon('heroicon-o-clock'),
                
            Stat::make('🏆 Event Payouts (This Week)', 'Rp ' . number_format($totalEventPayouts, 0, ',', '.'))
                ->description("Event rewards paid out")
                ->color('success')
                ->icon('heroicon-o-trophy'),
                
            Stat::make('📈 Net Income (This Week)', 'Rp ' . number_format($totalIncome - $totalWithdrawals - $totalEventPayouts, 0, ',', '.'))
                ->description("Income minus payouts")
                ->color($totalIncome - $totalWithdrawals - $totalEventPayouts >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
