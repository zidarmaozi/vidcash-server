<?php

namespace App\Filament\Widgets;

use App\Models\Video;
use App\Models\View;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TopVideosWidget extends ChartWidget
{
    protected static ?string $heading = 'Top Performing Videos';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '400px';
    
    public ?string $filter = 'week';
    public int $topCount = 8;
    
    protected function getFilters(): ?array
    {
        return [
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
        
        $topVideos = Video::withCount(['views as total_views'])
            ->withSum(['views as total_income' => function ($query) use ($startDate, $endDate) {
                $query->where('income_generated', true)
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->where('is_active', true)
            ->orderBy('total_income', 'desc')
            ->limit($this->topCount)
            ->get();
        
        $labels = $topVideos->map(fn($video) => strlen($video->title) > 20 ? substr($video->title, 0, 20) . '...' : $video->title)->toArray();
        $income = $topVideos->map(fn($video) => (float) ($video->total_income ?? 0))->toArray();
        $views = $topVideos->map(fn($video) => (int) $video->total_views)->toArray();
        
        return [
            'datasets' => [
                [
                    'label' => 'Income (IDR)',
                    'data' => $income,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Views Count',
                    'data' => $views,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    protected function getType(): string
    {
        return 'bar';
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
                    'beginAtZero' => true,
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
                    'beginAtZero' => true,
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
        $filter = $this->filter;
        
        return match ($filter) {
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
    
    protected function getFooter(): ?string
    {
        $dateRange = $this->getDateRange();
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
        $totalActiveVideos = Video::where('is_active', true)->count();
        $totalIncome = View::where('income_generated', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('income_amount');
            
        $avgIncomePerVideo = $totalActiveVideos > 0 ? $totalIncome / $totalActiveVideos : 0;
        
        return "Total Active Videos: {$totalActiveVideos} | Total Income: Rp " . number_format($totalIncome, 0, ',', '.') . 
               " | Avg Income/Video: Rp " . number_format($avgIncomePerVideo, 0, ',', '.');
    }
}
