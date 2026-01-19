<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UserGrowthChart extends ChartWidget
{
    protected static ?string $heading = '📈 Pertumbuhan Pengguna (30 Hari Terakhir)';

    protected static ?string $maxHeight = '300px';

    protected static ?int $sort = 5;

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        // Cache for 30 minutes
        return cache()->remember('user_growth_chart_30d', 1800, function () {
            $startDate = Carbon::now()->subDays(30);

            $data = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as new_users')
            )
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            return [
                'datasets' => [
                    [
                        'label' => 'User Baru',
                        'data' => $data->pluck('new_users')->all(),
                        'borderColor' => 'rgba(234, 179, 8, 1)', // Amber/Warning color
                        'backgroundColor' => 'rgba(234, 179, 8, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M'))->all(),
            ];
        });
    }

    protected function getType(): string
    {
        return 'line';
    }
}
