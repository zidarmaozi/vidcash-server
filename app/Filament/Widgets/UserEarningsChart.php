<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserEarningsChart extends ChartWidget
{
    public $record;

    protected static ?string $heading = 'Earnings Trend (Last 30 Days)';

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
                DB::raw('SUM(CASE WHEN views.income_generated = 1 THEN views.income_amount ELSE 0 END) as daily_income')
            )
            ->where('videos.user_id', $this->record->id)
            ->where('views.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Daily Earnings (Rp)',
                    'data' => $data->pluck('daily_income')->all(),
                    'borderColor' => 'rgba(22, 163, 74, 1)',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.1)',
                    'fill' => true,
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
