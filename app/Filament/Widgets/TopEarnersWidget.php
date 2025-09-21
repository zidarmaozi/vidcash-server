<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TopEarnersWidget extends ChartWidget
{
    protected static ?string $heading = 'Top Earners';
    protected static ?int $sort = 3;
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
        
        $topEarners = User::withCount(['videos as total_videos'])
            ->with(['videos.views' => function ($query) use ($startDate, $endDate) {
                $query->where('income_generated', true)
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($user) {
                $totalIncome = $user->videos->sum(function ($video) {
                    return $video->views->sum('income_amount');
                });
                
                return [
                    'name' => $user->name,
                    'income' => (float) $totalIncome,
                    'videos' => (int) $user->total_videos,
                ];
            })
            ->sortByDesc('income')
            ->take(10)
            ->values();
        
        return [
            'datasets' => [
                [
                    'label' => 'Income (IDR)',
                    'data' => $topEarners->pluck('income')->toArray(),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(220, 38, 127, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(236, 72, 153)',
                        'rgb(251, 146, 60)',
                        'rgb(239, 68, 68)',
                        'rgb(16, 185, 129)',
                        'rgb(99, 102, 241)',
                        'rgb(245, 158, 11)',
                        'rgb(220, 38, 127)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $topEarners->pluck('name')->toArray(),
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
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Income (IDR)',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { 
                            return "Income: Rp " + context.parsed.y.toLocaleString("id-ID");
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