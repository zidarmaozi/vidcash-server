<?php

namespace App\Filament\Widgets;

use App\Models\View;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class IncomeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Income Trend';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '400px';
    
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
    
    protected function getData(): array
    {
        $dateRange = $this->getDateRange();
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
        // Get daily income data
        $incomeData = View::where('income_generated', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(income_amount) as total_income'),
                DB::raw('COUNT(*) as total_views')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get all dates in range
        $dates = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }
        
        $income = [];
        $views = [];
        $labels = [];
        
        foreach ($dates as $date) {
            $dayData = $incomeData->firstWhere('date', $date);
            $income[] = $dayData ? (float) $dayData->total_income : 0;
            $views[] = $dayData ? (int) $dayData->total_views : 0;
            $labels[] = Carbon::parse($date)->format('M d');
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Daily Income (IDR)',
                    'data' => $income,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Views Count',
                    'data' => $views,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
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
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Income (IDR)',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Views Count',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { 
                            if (context.datasetIndex === 0) {
                                return "Income: Rp " + context.parsed.y.toLocaleString("id-ID");
                            } else {
                                return "Views: " + context.parsed.y.toLocaleString("id-ID");
                            }
                        }',
                    ],
                ],
            ],
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
