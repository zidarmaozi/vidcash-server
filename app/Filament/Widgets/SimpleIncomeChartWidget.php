<?php

namespace App\Filament\Widgets;

use App\Models\View;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SimpleIncomeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Income Trend (This Week)';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '400px';
    
    protected function getData(): array
    {
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();
        
        // Get daily income data for this week
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
        
        // Get all dates in the week
        $dates = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }
        
        // Fill in missing dates with 0
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
                    'borderWidth' => 2,
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
    
    protected function getFooter(): ?string
    {
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();
        
        $totalIncome = View::where('income_generated', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('income_amount');
            
        $totalViews = View::whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $avgIncomePerView = $totalViews > 0 ? $totalIncome / $totalViews : 0;
        
        return "Total Income: Rp " . number_format($totalIncome, 0, ',', '.') . 
               " | Total Views: " . number_format($totalViews) . 
               " | Avg Income/View: Rp " . number_format($avgIncomePerView, 2, ',', '.');
    }
}
