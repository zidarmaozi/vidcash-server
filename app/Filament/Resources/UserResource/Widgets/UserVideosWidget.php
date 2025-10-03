<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UserVideosWidget extends BaseWidget
{
    public ?User $record = null;
    
    protected static ?string $heading = '🎥 Video yang Diupload';
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        if (!$this->record) {
            return $table->query(\App\Models\Video::query()->where('id', 0));
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Video')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->title)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('video_code')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin!')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_safe_content')
                    ->label('Aman')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_income')
                    ->label('Pendapatan')
                    ->money('IDR')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.videos.show', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25])
            ->poll('30s')
            ->emptyStateHeading('Belum ada video')
            ->emptyStateDescription('User ini belum mengupload video apapun.')
            ->emptyStateIcon('heroicon-o-video-camera');
    }
}

