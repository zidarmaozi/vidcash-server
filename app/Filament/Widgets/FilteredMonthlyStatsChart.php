<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\Setting;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FilteredMonthlyStatsChart extends ChartWidget
{
    public ?array $dateRange = null;

    protected static ?string $heading = 'Statistik Views & Pendapatan';

    public function mount(?array $dateRange = null): void
    {
        // Initialize with parameter from parent or default values
        $this->dateRange = $dateRange ?? ['start' => null, 'end' => null];
    }

    public function updatedDateRange(): void
    {
        // This will be called when dateRange is updated from parent
    }

    protected function getData(): array
    {
        $query = $this->buildDateQuery();
        $cacheKey = 'monthly_stats_chart_' . md5(serialize($query));
        
        return cache()->remember($cacheKey, 60, function () use ($query) {
            // Build base query with date filtering
            $baseQuery = View::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as views'),
                DB::raw('SUM(CASE WHEN income_generated = 1 THEN income_amount ELSE 0 END) as total_income')
            );

            // Apply date filtering
            if ($query['start']) {
                $baseQuery->where('created_at', '>=', $query['start']);
            }
            if ($query['end']) {
                $baseQuery->where('created_at', '<=', $query['end']);
            }

            $data = $baseQuery
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            // Get current CPM for comparison
            $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);

            $dateRangeLabel = $this->getDateRangeLabel();

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
        });
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function buildDateQuery(): array
    {
        if (!$this->dateRange) {
            return ['start' => null, 'end' => null];
        }
        
        return $this->dateRange;
    }

    private function getDateRangeLabel(): string
    {
        if (!$this->dateRange || (!$this->dateRange['start'] && !$this->dateRange['end'])) {
            return 'Semua Data';
        }

        if ($this->dateRange['start'] && $this->dateRange['end']) {
            $start = $this->dateRange['start']->format('d M Y');
            $end = $this->dateRange['end']->format('d M Y');
            return "{$start} - {$end}";
        }

        if ($this->dateRange['start']) {
            return 'Sejak ' . $this->dateRange['start']->format('d M Y');
        }

        if ($this->dateRange['end']) {
            return 'Sampai ' . $this->dateRange['end']->format('d M Y');
        }

        return 'Semua Data';
    }
}
