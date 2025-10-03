<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserEarningsChartWidget extends ChartWidget
{
    public ?User $record = null;
    
    protected static ?string $heading = '💰 Trend Pendapatan (30 Hari Terakhir)';
    
    protected static ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        if (!$this->record) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $data = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->select(
                DB::raw('DATE(views.created_at) as date'),
                DB::raw('SUM(CASE WHEN views.income_generated = 1 THEN views.income_amount ELSE 0 END) as daily_income'),
                DB::raw('COUNT(CASE WHEN views.validation_passed = 1 THEN 1 END) as valid_views')
            )
            ->where('videos.user_id', $this->record->id)
            ->where('views.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        if ($data->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Pendapatan Harian (Rp)',
                        'data' => [],
                    ],
                ],
                'labels' => ['Tidak ada data'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan Harian (Rp)',
                    'data' => $data->pluck('daily_income')->all(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
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
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString(); }',
                    ],
                ],
            ],
        ];
    }
}

