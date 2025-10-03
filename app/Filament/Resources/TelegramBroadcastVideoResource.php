<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TelegramBroadcastVideoResource\Pages;
use App\Models\TelegramBroadcastVideo;
use App\Models\Video;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TelegramBroadcastVideoResource extends Resource
{
    protected static ?string $model = TelegramBroadcastVideo::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    
    protected static ?string $navigationLabel = 'Broadcasted Videos';
    
    protected static ?string $modelLabel = 'Broadcasted Video';
    
    protected static ?string $pluralModelLabel = 'Broadcasted Videos';
    
    protected static ?string $navigationGroup = 'Telegram';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only resource, no form needed
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('video.thumbnail_path')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->height(60)
                    ->width(80)
                    ->defaultImageUrl('https://via.placeholder.com/80x60/e5e7eb/6b7280?text=No+Thumbnail')
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
                
                Tables\Columns\TextColumn::make('video.title')
                    ->label('Video Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (TelegramBroadcastVideo $record) => $record->video->title),
                
                Tables\Columns\TextColumn::make('video.user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('video.video_code')
                    ->label('Video Code')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Video code copied!')
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('video.is_active')
                    ->label('Video Status')
                    ->badge()
                    ->color(fn (TelegramBroadcastVideo $record): string => $record->video->is_active ? 'success' : 'danger')
                    ->formatStateUsing(fn (TelegramBroadcastVideo $record): string => $record->video->is_active ? 'Active' : 'Inactive')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('video.is_safe_content')
                    ->label('Content Safety')
                    ->badge()
                    ->color(fn (TelegramBroadcastVideo $record): string => $record->video->is_safe_content ? 'success' : 'warning')
                    ->formatStateUsing(fn (TelegramBroadcastVideo $record): string => $record->video->is_safe_content ? '🛡️ Safe' : '⚠️ Unsafe'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Broadcasted At')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->color('info')
                    ->tooltip(fn (TelegramBroadcastVideo $record) => $record->created_at->diffForHumans()),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->getStateUsing(fn (TelegramBroadcastVideo $record) => $record->video->views()->count())
                    ->sortable(query: function ($query, string $direction) {
                        return $query->withCount('video')
                            ->orderBy('video_count', $direction);
                    })
                    ->color('info'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('video.user', 'name')
                    ->label('Filter by Owner'),
                
                Tables\Filters\SelectFilter::make('video_status')
                    ->label('Video Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] === 'active') {
                            return $query->whereHas('video', fn ($q) => $q->where('is_active', true));
                        }
                        if ($state['value'] === 'inactive') {
                            return $query->whereHas('video', fn ($q) => $q->where('is_active', false));
                        }
                    }),
                
                Tables\Filters\SelectFilter::make('safe_content')
                    ->label('Content Safety')
                    ->options([
                        'safe' => 'Safe Content',
                        'unsafe' => 'Unsafe Content',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] === 'safe') {
                            return $query->whereHas('video', fn ($q) => $q->where('is_safe_content', true));
                        }
                        if ($state['value'] === 'unsafe') {
                            return $query->whereHas('video', fn ($q) => $q->where('is_safe_content', false));
                        }
                    }),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('broadcasted_from')
                            ->label('Broadcasted From'),
                        \Filament\Forms\Components\DatePicker::make('broadcasted_until')
                            ->label('Broadcasted Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['broadcasted_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['broadcasted_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewVideo')
                    ->label('View Video')
                    ->icon('heroicon-o-eye')
                    ->url(fn (TelegramBroadcastVideo $record) => route('filament.admin.resources.videos.show', $record->video))
                    ->openUrlInNewTab()
                    ->color('info'),
                
                Tables\Actions\ViewAction::make()
                    ->label('View Details'),
            ])
            ->bulkActions([
                // Read-only resource, no bulk actions
            ]);
    }
    
    public static function canCreate(): bool
    {
        return false; // Read-only resource
    }
    
    public static function canEdit($record): bool
    {
        return false; // Read-only resource
    }
    
    public static function canDelete($record): bool
    {
        return false; // Read-only resource
    }
    
    public static function canDeleteAny(): bool
    {
        return false; // Read-only resource
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTelegramBroadcastVideos::route('/'),
            'view' => Pages\ViewTelegramBroadcastVideo::route('/{record}'),
        ];
    }
}
