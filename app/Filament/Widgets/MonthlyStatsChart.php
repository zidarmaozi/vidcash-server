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
        $data = View::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as views')
            )
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Get actual CPM setting from database
        $cpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $data->pluck('views')->all(),
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                ],
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data->pluck('views')->map(fn ($view) => $view * $cpm)->all(),
                    'borderColor' => 'rgba(22, 163, 74, 1)',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.5)',
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