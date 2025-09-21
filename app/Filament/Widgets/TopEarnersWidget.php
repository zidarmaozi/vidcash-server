<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\View;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TopEarnersWidget extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Earners (Minggu Ini)';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $thisWeek = Carbon::now()->startOfWeek();
        
        $topEarners = User::with(['videos.views' => function($query) use ($thisWeek) {
            $query->where('created_at', '>=', $thisWeek)
                  ->where('income_generated', true);
        }])
        ->get()
        ->map(function($user) {
            $totalIncome = $user->videos->sum(function($video) {
                return $video->views->sum('income_amount');
            });
            
            return [
                'name' => $user->name,
                'income' => $totalIncome
            ];
        })
        ->sortByDesc('income')
        ->take(5);

        $labels = $topEarners->pluck('name')->toArray();
        $data = $topEarners->pluck('income')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Income (Rp)',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)',
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
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return "Income: Rp " + context.parsed.y.toLocaleString("id-ID"); }'
                    ]
                ]
            ]
        ];
    }
}
