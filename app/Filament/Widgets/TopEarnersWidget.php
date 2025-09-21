<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\View;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TopEarnersWidget extends ChartWidget
{
    protected static ?string $heading = 'Top Earners';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '400px';
    
    public ?string $filter = 'week';
    public int $topCount = 10;
    
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
        
        $topEarners = User::withCount(['videos as total_videos'])
            ->withSum(['videos as total_income' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('views', function ($q) use ($startDate, $endDate) {
                    $q->where('income_generated', true)
                      ->whereBetween('created_at', [$startDate, $endDate]);
                });
            }])
            ->orderBy('total_income', 'desc')
            ->limit($this->topCount)
            ->get();
        
        $labels = $topEarners->map(fn($user) => $user->name)->toArray();
        $income = $topEarners->map(fn($user) => (float) ($user->total_income ?? 0))->toArray();
        $videos = $topEarners->map(fn($user) => (int) $user->total_videos)->toArray();
        
        return [
            'datasets' => [
                [
                    'label' => 'Income (IDR)',
                    'data' => $income,
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
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Income (IDR)',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Users',
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
                        'afterLabel' => 'function(context) {
                            const userIndex = context.dataIndex;
                            const userData = window.topEarnersData || [];
                            if (userData[userIndex]) {
                                return "Videos: " + userData[userIndex].videos;
                            }
                            return "";
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
        
        $totalUsers = User::whereHas('videos.views', function ($query) use ($startDate, $endDate) {
            $query->where('income_generated', true)
                  ->whereBetween('created_at', [$startDate, $endDate]);
        })->count();
        
        $totalIncome = View::where('income_generated', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('income_amount');
            
        $avgIncomePerUser = $totalUsers > 0 ? $totalIncome / $totalUsers : 0;
        
        return "Total Active Users: {$totalUsers} | Total Income: Rp " . number_format($totalIncome, 0, ',', '.') . 
               " | Avg Income/User: Rp " . number_format($avgIncomePerUser, 0, ',', '.');
    }
}
