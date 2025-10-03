<?php

namespace App\Filament\Widgets;

use App\Models\View;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ViewerSourceStats extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        // Cache for 10 minutes
        return cache()->remember('viewer_source_stats', 600, function() {
            // Get counts for each source
            $telegramCount = View::where('via', 'telegram')->count();
            $directCount = View::where('via', 'direct')->count();
            $relatedCount = View::where('via', 'related')->count();
            $unknownCount = View::whereNull('via')->count();
            
            $totalViews = View::count();
            
            // Calculate percentages
            $telegramPercent = $totalViews > 0 ? round(($telegramCount / $totalViews) * 100, 1) : 0;
            $directPercent = $totalViews > 0 ? round(($directCount / $totalViews) * 100, 1) : 0;
            $relatedPercent = $totalViews > 0 ? round(($relatedCount / $totalViews) * 100, 1) : 0;
            $unknownPercent = $totalViews > 0 ? round(($unknownCount / $totalViews) * 100, 1) : 0;

            return [
                Stat::make('📱 Telegram Views', number_format($telegramCount))
                    ->description("{$telegramPercent}% dari total views")
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->chart($this->generateChartData($telegramCount, 7)),
                
                Stat::make('🔗 Direct Views', number_format($directCount))
                    ->description("{$directPercent}% dari total views")
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->chart($this->generateChartData($directCount, 7)),
                
                Stat::make('🎬 Related Views', number_format($relatedCount))
                    ->description("{$relatedPercent}% dari total views")
                    ->icon('heroicon-o-film')
                    ->color('warning')
                    ->chart($this->generateChartData($relatedCount, 7)),
                
                Stat::make('❓ Unknown Views', number_format($unknownCount))
                    ->description("{$unknownPercent}% dari total views")
                    ->icon('heroicon-o-question-mark-circle')
                    ->color('gray')
                    ->chart($this->generateChartData($unknownCount, 7)),
            ];
        });
    }

    /**
     * Generate simple chart data for last N days
     */
    private function generateChartData(int $currentCount, int $days): array
    {
        // Simple trend data (you can enhance this to show actual daily data)
        $data = [];
        $variation = max(1, round($currentCount * 0.1));
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $data[] = max(0, $currentCount + rand(-$variation, $variation));
        }
        
        return $data;
    }
}

