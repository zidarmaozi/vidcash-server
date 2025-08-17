<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ShowUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.show-user';

    public function getTitle(): string
    {
        return "User Profile: {$this->record->name}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit User')
                ->icon('heroicon-o-pencil')
                ->url(fn () => route('filament.admin.resources.users.edit', $this->record))
                ->color('primary'),
            
            Action::make('view_videos')
                ->label('View All Videos')
                ->icon('heroicon-o-video-camera')
                ->url(fn () => route('filament.admin.resources.videos.index', ['tableFilters[user][value]' => $this->record->id]))
                ->color('info')
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UserVideosTable::class,
            UserEarningsChart::class,
            UserViewsChart::class,
        ];
    }
}

// User Stats Overview Widget
class UserStatsOverview extends BaseWidget
{
    public $record;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $user = $this->record;
        
        // Calculate financial stats
        $totalIncome = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->where('videos.user_id', $user->id)
            ->where('views.income_generated', true)
            ->sum('views.income_amount');

        $pendingWithdrawals = DB::table('withdrawals')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $pendingEventPayouts = DB::table('event_payouts')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('prize_amount');

        // Calculate video performance stats
        $totalViews = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->where('videos.user_id', $user->id)
            ->where('views.validation_passed', true)
            ->count();

        $totalViewsAll = DB::table('views')
            ->join('videos', 'views.video_id', '=', 'videos.id')
            ->where('videos.user_id', $user->id)
            ->count();

        $successRate = $totalViewsAll > 0 ? round(($totalViews / $totalViewsAll) * 100, 1) : 0;

        return [
            Stat::make('Current Balance', 'Rp' . number_format($user->balance, 0, ',', '.'))
                ->description('Available balance for withdrawal')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Total Income Generated', 'Rp' . number_format($totalIncome, 0, ',', '.'))
                ->description('All-time earnings from videos')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('Total Withdrawn', 'Rp' . number_format($user->total_withdrawn, 0, ',', '.'))
                ->description('Amount already withdrawn')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),

            Stat::make('Pending Amounts', 'Rp' . number_format($pendingWithdrawals + $pendingEventPayouts, 0, ',', '.'))
                ->description('Withdrawals + Event payouts pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Videos', $user->videos()->count())
                ->description('Videos uploaded')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('primary'),

            Stat::make('Total Views', number_format($totalViews))
                ->description("Success rate: {$successRate}%")
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),
        ];
    }
}

// User Videos Table Widget
class UserVideosTable extends TableWidget
{
    public $record;

    protected static ?string $heading = 'Uploaded Videos';

    public function table(Table $table): Table
    {
        if (!$this->record) {
            return $table->query(DB::table('videos')->where('id', 0)); // Empty query
        }

        return $table
            ->query(
                $this->record->videos()
                    ->withCount(['views' => function ($query) {
                        $query->where('validation_passed', true);
                    }])
                    ->withSum(['views as total_income' => function ($query) {
                        $query->where('income_generated', true);
                    }], 'income_amount')
                    ->getQuery()
            )
            ->columns([
                TextColumn::make('video_code')
                    ->label('Video Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('generated_link')
                    ->label('Video Link')
                    ->url(fn ($record) => $record->generated_link)
                    ->openUrlInNewTab()
                    ->searchable()
                    ->limit(50),

                TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable()
                    ->color('info'),

                TextColumn::make('total_income')
                    ->label('Income Generated')
                    ->money('IDR')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->color('gray'),
            ])
            ->actions([
                Action::make('view_video')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.videos.show', $record))
                    ->openUrlInNewTab()
                    ->color('info'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}

// User Earnings Chart Widget
class UserEarningsChart extends ChartWidget
{
    public $record;

    protected static ?string $heading = 'Earnings Trend (Last 30 Days)';

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

// User Views Chart Widget
class UserViewsChart extends ChartWidget
{
    public $record;

    protected static ?string $heading = 'Views Trend (Last 30 Days)';

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
