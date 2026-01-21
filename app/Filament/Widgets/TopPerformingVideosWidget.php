<?php

namespace App\Filament\Widgets;

use App\Models\Video;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class TopPerformingVideosWidget extends BaseWidget
{
    protected static ?string $heading = '🔥 Top Performing Videos';

    protected static ?int $sort = 8;

    public ?array $dateRange = null;

    public function mount(?array $dateRange = null): void
    {
        $this->dateRange = $dateRange ?? ['start' => null, 'end' => null];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Video::query()
                    ->where('is_active', true)
                    ->withCount([
                        'views' => function ($query) {
                            if ($this->dateRange['start']) {
                                $query->where('created_at', '>=', $this->dateRange['start']);
                            }
                            if ($this->dateRange['end']) {
                                $query->where('created_at', '<=', $this->dateRange['end']);
                            }
                        }
                    ])
                    // We also want to sum the income generated
                    ->withSum([
                        'views as total_income' => function ($query) {
                            $query->where('income_generated', true);
                            if ($this->dateRange['start']) {
                                $query->where('created_at', '>=', $this->dateRange['start']);
                            }
                            if ($this->dateRange['end']) {
                                $query->where('created_at', '<=', $this->dateRange['end']);
                            }
                        }
                    ], 'income_amount')
                    ->orderByDesc('views_count')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Video Title')
                    ->limit(30)
                    ->tooltip(fn(Model $record): string => $record->title),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->limit(15),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('total_income')
                    ->label('Income')
                    ->money('IDR')
                    ->color('success'),
            ])
            ->paginated(false);
    }
}
