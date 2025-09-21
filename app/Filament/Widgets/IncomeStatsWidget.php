<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\Withdrawal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class IncomeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Today's income
        $todayIncome = View::whereDate('created_at', $today)
            ->where('income_generated', true)
            ->sum('income_amount');

        // Yesterday's income
        $yesterdayIncome = View::whereDate('created_at', $yesterday)
            ->where('income_generated', true)
            ->sum('income_amount');

        // This week's income
        $thisWeekIncome = View::where('created_at', '>=', $thisWeek)
            ->where('income_generated', true)
            ->sum('income_amount');

        // Last week's income
        $lastWeekIncome = View::whereBetween('created_at', [$lastWeek, $thisWeek])
            ->where('income_generated', true)
            ->sum('income_amount');

        // This month's income
        $thisMonthIncome = View::where('created_at', '>=', $thisMonth)
            ->where('income_generated', true)
            ->sum('income_amount');

        // Last month's income
        $lastMonthIncome = View::whereBetween('created_at', [$lastMonth, $thisMonth])
            ->where('income_generated', true)
            ->sum('income_amount');

        // Calculate growth
        $todayGrowth = $yesterdayIncome > 0 ? (($todayIncome - $yesterdayIncome) / $yesterdayIncome) * 100 : 0;
        $weekGrowth = $lastWeekIncome > 0 ? (($thisWeekIncome - $lastWeekIncome) / $lastWeekIncome) * 100 : 0;
        $monthGrowth = $lastMonthIncome > 0 ? (($thisMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100 : 0;

        return [
            Stat::make('Hari Ini', 'Rp ' . number_format($todayIncome, 0, ',', '.'))
                ->description($todayGrowth >= 0 ? '+' . number_format($todayGrowth, 1) . '% dari kemarin' : number_format($todayGrowth, 1) . '% dari kemarin')
                ->descriptionIcon($todayGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($todayGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Minggu Ini', 'Rp ' . number_format($thisWeekIncome, 0, ',', '.'))
                ->description($weekGrowth >= 0 ? '+' . number_format($weekGrowth, 1) . '% dari minggu lalu' : number_format($weekGrowth, 1) . '% dari minggu lalu')
                ->descriptionIcon($weekGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($weekGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Bulan Ini', 'Rp ' . number_format($thisMonthIncome, 0, ',', '.'))
                ->description($monthGrowth >= 0 ? '+' . number_format($monthGrowth, 1) . '% dari bulan lalu' : number_format($monthGrowth, 1) . '% dari bulan lalu')
                ->descriptionIcon($monthGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Total Views', View::count())
                ->description('Total semua views')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
        ];
    }
}
