<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\Video;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class IncomeStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $dateRange = $this->getDateRange();
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Get data for selected period
        $totalIncome = View::whereBetween('created_at', [$startDate, $endDate])
            ->where('income_generated', true)
            ->sum('income_amount');

        $totalViews = View::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalVideos = Video::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $activeVideos = Video::whereBetween('created_at', [$startDate, $endDate])
            ->where('is_active', true)
            ->count();

        // Get previous period for comparison
        $previousPeriod = $this->getPreviousPeriod($startDate, $endDate);
        $previousIncome = View::whereBetween('created_at', [$previousPeriod['start'], $previousPeriod['end']])
            ->where('income_generated', true)
            ->sum('income_amount');

        // Calculate growth
        $growth = $previousIncome > 0 ? (($totalIncome - $previousIncome) / $previousIncome) * 100 : 0;

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalIncome, 0, ',', '.'))
                ->description($growth >= 0 ? '+' . number_format($growth, 1) . '% dari periode sebelumnya' : number_format($growth, 1) . '% dari periode sebelumnya')
                ->descriptionIcon($growth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($growth >= 0 ? 'success' : 'danger'),

            Stat::make('Total Views', number_format($totalViews))
                ->description('Views dalam periode terpilih')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),

            Stat::make('Total Video', number_format($totalVideos))
                ->description('Video dalam periode terpilih')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('warning'),

            Stat::make('Video Aktif', number_format($activeVideos))
                ->description('Video aktif dalam periode terpilih')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }

    private function getDateRange(): array
    {
        // Get date range from parent page
        $parentPage = $this->getParentPage();
        if ($parentPage && method_exists($parentPage, 'getDateRange')) {
            return $parentPage->getDateRange();
        }

        // Fallback to today if no parent page
        return [
            'start' => Carbon::today(),
            'end' => Carbon::today()->endOfDay(),
        ];
    }

    private function getParentPage()
    {
        // Try to get the parent page instance
        $livewire = app('livewire')->current();
        return $livewire;
    }

    private function getPreviousPeriod($startDate, $endDate): array
    {
        $duration = $endDate->diffInDays($startDate);
        
        return [
            'start' => $startDate->copy()->subDays($duration + 1),
            'end' => $startDate->copy()->subDay(),
        ];
    }
}
