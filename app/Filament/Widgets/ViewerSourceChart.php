<?php

namespace App\Filament\Widgets;

use App\Models\View;
use Filament\Widgets\ChartWidget;

class ViewerSourceChart extends ChartWidget
{
    protected static ?string $heading = '📊 Sumber Viewer (Viewer Source Distribution)';

    protected static ?int $sort = 6;

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        // Cache for 10 minutes
        return cache()->remember('viewer_source_chart_data', 600, function () {
            // Get counts for each source
            $telegramCount = View::where('via', 'telegram')->count();
            $directCount = View::where('via', 'direct')->count();
            $relatedCount = View::where('via', 'related')->count();
            $unknownCount = View::whereNull('via')->count();

            return [
                'datasets' => [
                    [
                        'label' => 'Viewer Source',
                        'data' => [$telegramCount, $directCount, $relatedCount, $unknownCount],
                        'backgroundColor' => [
                            'rgb(59, 130, 246)',   // Blue - Telegram
                            'rgb(34, 197, 94)',    // Green - Direct
                            'rgb(251, 146, 60)',   // Orange - Related
                            'rgb(156, 163, 175)',  // Gray - Unknown
                        ],
                        'borderColor' => [
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
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
                    '❓ Unknown (' . $unknownCount . ')',
                ],
            ];
        });
    }

    protected function getType(): string
    {
        return 'doughnut'; // Circle/Donut chart
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

