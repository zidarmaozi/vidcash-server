<?php

namespace App\Filament\Widgets;

use App\Models\View;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class IncomeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Income Trend (7 Hari Terakhir)';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $income = View::whereDate('created_at', $date)
                ->where('income_generated', true)
                ->sum('income_amount');
            
            $data[] = $income;
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
}
