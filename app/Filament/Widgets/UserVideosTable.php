<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;

class UserVideosTable extends TableWidget
{
    public $record;

    protected static ?string $heading = 'Uploaded Videos';

    public function mount($record = null): void
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        if (!$this->record) {
            return $table->query(\App\Models\Video::where('id', 0)); // Empty Eloquent query
        }

        return $table
            ->query(
                \App\Models\Video::where('user_id', $this->record->id)
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
                    ->getStateUsing(function ($record) {
                        return $record->views()->where('validation_passed', true)->count();
                    })
                    ->sortable()
                    ->color('info'),

                TextColumn::make('total_income')
                    ->label('Income Generated')
                    ->getStateUsing(function ($record) {
                        return $record->views()->where('income_generated', true)->sum('income_amount');
                    })
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
