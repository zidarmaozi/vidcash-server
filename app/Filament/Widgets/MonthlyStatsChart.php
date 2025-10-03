<?php

namespace App\Filament\Widgets;

use App\Models\View;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyStatsChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Views & Pendapatan';
    
    protected static ?string $maxHeight = '300px';
    
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Get data for last 30 days (more useful than current month only)
        $startDate = Carbon::now()->subDays(30);
        
        $data = View::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_views'),
                DB::raw('SUM(CASE WHEN validation_passed = 1 THEN 1 ELSE 0 END) as validated_views'),
                DB::raw('SUM(CASE WHEN income_generated = 1 THEN income_amount ELSE 0 END) as total_income')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Calculate totals for description
        $totalViews = $data->sum('total_views');
        $totalIncome = $data->sum('total_income');
        $avgViewsPerDay = $data->count() > 0 ? round($totalViews / $data->count()) : 0;

        return [
            'datasets' => [
                [
                    'label' => 'Total Views',
                    'data' => $data->pluck('total_views')->all(),
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Views Tervalidasi',
                    'data' => $data->pluck('validated_views')->all(),
                    'borderColor' => 'rgba(168, 85, 247, 1)',
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data->pluck('total_income')->all(),
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ]
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('d M'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Views',
                    ],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Pendapatan (Rp)',
                    ],
                ],
            ],
        ];
    }

    public function getDescription(): ?string
    {
        $startDate = Carbon::now()->subDays(30);
        $data = View::select(
                DB::raw('COUNT(*) as total_views'),
                DB::raw('SUM(CASE WHEN income_generated = 1 THEN income_amount ELSE 0 END) as total_income')
            )
            ->where('created_at', '>=', $startDate)
            ->first();

        $totalViews = number_format($data->total_views ?? 0);
        $totalIncome = number_format($data->total_income ?? 0, 0, ',', '.');

        return "30 Hari Terakhir: {$totalViews} views | Rp{$totalIncome} pendapatan";
    }
}