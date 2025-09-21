<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\Withdrawal;
use App\Models\EventPayout;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class IncomeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    public ?string $filter = 'week';
    
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'week' => 'This Week',
            'last_week' => 'Last Week',
            'month' => 'This Month',
            'last_month' => 'Last Month',
            'quarter' => 'This Quarter',
            'year' => 'This Year',
        ];
    }
    
    protected function getStats(): array
    {
        $dateRange = $this->getDateRange();
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
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
            
        $netIncome = $totalIncome - $totalWithdrawals - $totalEventPayouts;
        
        return [
            Stat::make('💰 Total Income', 'Rp ' . number_format($totalIncome, 0, ',', '.'))
                ->description("From {$validatedViews} validated views")
                ->descriptionIcon('heroicon-m-eye')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),
                
            Stat::make('👁️ Total Views', number_format($totalViews))
                ->description("Valid: {$validatedViews} | Failed: {$failedViews}")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info')
                ->icon('heroicon-o-eye'),
                
            Stat::make('✅ Validation Rate', $totalViews > 0 ? round(($validatedViews / $totalViews) * 100, 1) . '%' : '0%')
                ->description("Success rate")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($totalViews > 0 && ($validatedViews / $totalViews) >= 0.7 ? 'success' : 'warning')
                ->icon('heroicon-o-check-circle'),
                
            Stat::make('📊 Avg Income/View', 'Rp ' . number_format($avgIncomePerView, 2, ',', '.'))
                ->description('Average income per view')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->icon('heroicon-o-calculator'),
                
            Stat::make('💸 Withdrawals', 'Rp ' . number_format($totalWithdrawals, 0, ',', '.'))
                ->description("Confirmed withdrawals")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->icon('heroicon-o-banknotes'),
                
            Stat::make('⏳ Pending', 'Rp ' . number_format($pendingWithdrawals, 0, ',', '.'))
                ->description("Awaiting approval")
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger')
                ->icon('heroicon-o-clock'),
                
            Stat::make('🏆 Event Payouts', 'Rp ' . number_format($totalEventPayouts, 0, ',', '.'))
                ->description("Event rewards paid out")
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->icon('heroicon-o-trophy'),
                
            Stat::make('📈 Net Income', 'Rp ' . number_format($netIncome, 0, ',', '.'))
                ->description("Income minus payouts")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($netIncome >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
    
    private function getDateRange(): array
    {
        return match ($this->filter) {
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'yesterday' => [
                'start' => now()->subDay()->startOfDay(),
                'end' => now()->subDay()->endOfDay(),
            ],
            'week' => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
            ],
            'last_week' => [
                'start' => now()->subWeek()->startOfWeek(),
                'end' => now()->subWeek()->endOfWeek(),
            ],
            'month' => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
            ],
            'last_month' => [
                'start' => now()->subMonth()->startOfMonth(),
                'end' => now()->subMonth()->endOfMonth(),
            ],
            'quarter' => [
                'start' => now()->startOfQuarter(),
                'end' => now()->endOfQuarter(),
            ],
            'year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
            ],
            default => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
            ],
        };
    }
}
