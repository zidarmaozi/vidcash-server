<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\Setting;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyStatsChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Views & Pendapatan Bulan Ini';

    protected function getData(): array
    {
        // Get data for current month using STORED income amounts
        $data = View::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as views'),
                DB::raw('SUM(CASE WHEN income_generated = 1 THEN income_amount ELSE 0 END) as total_income')
            )
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Get current CPM for comparison
        $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $data->pluck('views')->all(),
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                ],
                [
                    'label' => 'Pendapatan (Rp) - STORED',
                    'data' => $data->pluck('total_income')->all(),
                    'borderColor' => 'rgba(22, 163, 74, 1)',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.5)',
                ],
                [
                    'label' => 'Pendapatan (Rp) - CALCULATED',
                    'data' => $data->pluck('views')->map(fn ($view) => $view * $currentCpm)->all(),
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                ]
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('d M'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}