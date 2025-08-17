<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserViewsChart extends ChartWidget
{
    public $record;

    protected static ?string $heading = 'Views Trend (Last 30 Days)';

    public function mount($record = null): void
    {
        $this->record = $record;
    }

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
                DB::raw('SUM(CASE WHEN views.validation_passed = 1 THEN 1 ELSE 0 END) as valid_views'),
                DB::raw('COUNT(*) as total_views')
            )
            ->where('videos.user_id', $this->record->id)
            ->where('views.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Valid Views',
                    'data' => $data->pluck('valid_views')->all(),
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Total Views',
                    'data' => $data->pluck('total_views')->all(),
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => false,
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('d M'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
