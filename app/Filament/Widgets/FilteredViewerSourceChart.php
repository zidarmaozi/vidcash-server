<?php

namespace App\Filament\Widgets;

use App\Models\View;
use Filament\Widgets\ChartWidget;

class FilteredViewerSourceChart extends ChartWidget
{
    protected static ?string $heading = '📊 Sumber Viewer (Filtered)';

    protected static ?int $sort = 6;

    protected static bool $isLazy = true;

    public ?array $dateRange = null;

    public function mount(?array $dateRange = null): void
    {
        $this->dateRange = $dateRange ?? ['start' => null, 'end' => null];
    }

    protected function getData(): array
    {
        $query = $this->dateRange;
        $cacheKey = 'filtered_viewer_source_chart_' . md5(serialize($query));

        // Cache for 5 minutes
        return cache()->remember($cacheKey, 300, function () use ($query) {
            // Helper to apply date filter
            $applyFilter = function ($q) use ($query) {
                return $q->when($query['start'] ?? null, fn($sq) => $sq->where('created_at', '>=', $query['start']))
                    ->when($query['end'] ?? null, fn($sq) => $sq->where('created_at', '<=', $query['end']));
            };

            // Get counts for each source with date filter
            $telegramCount = $applyFilter(View::where('via', 'telegram'))->count();
            $directCount = $applyFilter(View::where('via', 'direct'))->count();
            $relatedCount = $applyFilter(View::where('via', 'related'))->count();
            $folderCount = $applyFilter(View::where('via', 'folder'))->count();
            $unknownCount = $applyFilter(View::whereNull('via'))->count();

            return [
                'datasets' => [
                    [
                        'label' => 'Viewer Source',
                        'data' => [$telegramCount, $directCount, $relatedCount, $folderCount, $unknownCount],
                        'backgroundColor' => [
                            'rgb(59, 130, 246)',   // Blue - Telegram
                            'rgb(34, 197, 94)',    // Green - Direct
                            'rgb(128, 0, 128)',   // Purple - Related
                            'rgb(251, 146, 60)',   // Orange - Folder
                            'rgb(156, 163, 175)',  // Gray - Unknown
                        ],
                        'borderColor' => [
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
                            'rgb(128, 0, 128)',
                            'rgb(251, 146, 60)',
                            'rgb(156, 163, 175)',
                        ],
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => [
                    '📱 Telegram (' . $telegramCount . ')',
                    '🔗 Direct (' . $directCount . ')',
                    '🎬 Related (' . $relatedCount . ')',
                    '📁 Folder (' . $folderCount . ')',
                    '❓ Unknown (' . $unknownCount . ')',
                ],
            ];
        });
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'maintainAspectRatio' => true,
            'responsive' => true,
        ];
    }
}
