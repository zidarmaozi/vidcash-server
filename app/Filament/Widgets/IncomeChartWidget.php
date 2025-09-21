<?php

namespace App\Filament\Widgets;

use App\Models\View;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class IncomeChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Income Trend';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $dateRange = $this->getDateRange();
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        $data = [];
        $labels = [];

        // Determine interval based on date range
        $interval = $this->getInterval($startDate, $endDate);
        
        if ($interval === 'daily') {
            // Daily data
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $labels[] = $current->format('M d');
                
                $income = View::whereDate('created_at', $current)
                    ->where('income_generated', true)
                    ->sum('income_amount');
                
                $data[] = $income;
                $current->addDay();
            }
        } else {
            // Weekly data
            $current = $startDate->copy()->startOfWeek();
            while ($current->lte($endDate)) {
                $weekEnd = $current->copy()->endOfWeek();
                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate;
                }
                
                $labels[] = $current->format('M d') . ' - ' . $weekEnd->format('M d');
                
                $income = View::whereBetween('created_at', [$current, $weekEnd])
                    ->where('income_generated', true)
                    ->sum('income_amount');
                
                $data[] = $income;
                $current->addWeek();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Income (Rp)',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }'
                    ]
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return "Income: Rp " + context.parsed.y.toLocaleString("id-ID"); }'
                    ]
                ]
            ]
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

    private function getInterval($startDate, $endDate): string
    {
        $days = $startDate->diffInDays($endDate);
        
        // If more than 30 days, use weekly intervals
        return $days > 30 ? 'weekly' : 'daily';
    }
}
