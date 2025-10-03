<?php

namespace App\Filament\Widgets;

use App\Models\View;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DailyIncomeChart extends ChartWidget
{
    protected static ?string $heading = '💰 Daily Income Overview';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        // Cache for 10 minutes for better performance
        return cache()->remember('daily_income_chart_30d', 600, function() {
            // Get last 30 days income data
            $startDate = Carbon::now()->subDays(30);
            
            $data = View::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(CASE WHEN income_generated = 1 THEN income_amount ELSE 0 END) as daily_income')
                )
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            return [
                'datasets' => [
                    [
                        'label' => 'Daily Income (Rp)',
                        'data' => $data->pluck('daily_income')->all(),
                        'borderColor' => 'rgba(34, 197, 94, 1)',
                        'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('d M'))->all(),
            ];
        });
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Income (Rp)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Date',
                    ],
                ],
            ],
        ];
    }

    public function getDescription(): ?string
    {
        return cache()->remember('daily_income_description', 600, function() {
            $startDate = Carbon::now()->subDays(30);
            
            $stats = View::select(
                    DB::raw('SUM(CASE WHEN income_generated = 1 THEN income_amount ELSE 0 END) as total_income'),
                    DB::raw('COUNT(*) as total_views')
                )
                ->where('created_at', '>=', $startDate)
                ->first();

            $totalIncome = $stats->total_income ?? 0;
            $totalViews = $stats->total_views ?? 0;
            $avgPerDay = $totalIncome > 0 ? round($totalIncome / 30) : 0;

            return "Last 30 Days: Rp" . number_format($totalIncome, 0, ',', '.') . " total | Rp" . number_format($avgPerDay, 0, ',', '.') . " avg/day | " . number_format($totalViews) . " views";
        });
    }
}

