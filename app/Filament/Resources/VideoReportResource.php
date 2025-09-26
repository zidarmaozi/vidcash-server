<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoReportResource\Pages;
use App\Models\VideoReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class VideoReportResource extends Resource
{
    protected static ?string $model = VideoReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Video Reports';
    protected static ?string $modelLabel = 'Video Report';
    protected static ?string $pluralModelLabel = 'Video Reports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('video_id')
                    ->relationship('video', 'title')
                    ->required()
                    ->searchable(),
                Forms\Components\Textarea::make('description')
                    ->label('Report Description')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('reporter_ip')
                    ->label('Reporter IP')
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'resolved' => 'Resolved',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('video.title')
                    ->label('Video Title')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('video.video_code')
                    ->label('Video Code')
                    ->searchable()
                    ->copyable()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Report Description')
                    ->limit(50)
                    ->tooltip(function (VideoReport $record) {
                        return $record->description;
                    }),
                Tables\Columns\TextColumn::make('reporter_ip')
                    ->label('Reporter IP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewed' => 'info',
                        'resolved' => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reported At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'resolved' => 'Resolved',
                    ]),
                Tables\Filters\SelectFilter::make('video_id')
                    ->relationship('video', 'title')
                    ->label('Filter by Video')
                    ->searchable(),
                Tables\Filters\Filter::make('recent')
                    ->label('Recent Reports (Last 24 hours)')
                    ->query(fn ($query) => $query->where('created_at', '>=', now()->subDay())),
            ])
            ->actions([
                Action::make('view_video')
                    ->label('View Video')
                    ->icon('heroicon-o-eye')
                    ->url(fn (VideoReport $record) => route('filament.admin.resources.videos.show', $record->video))
                    ->openUrlInNewTab()
                    ->color('info'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_reviewed')
                        ->label('Mark as Reviewed')
                        ->icon('heroicon-o-check-circle')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'reviewed']);
                            });
                            
                            Notification::make()
                                ->title('Reports marked as reviewed')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('mark_resolved')
                        ->label('Mark as Resolved')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'resolved']);
                            });
                            
                            Notification::make()
                                ->title('Reports marked as resolved')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideoReports::route('/'),
            'create' => Pages\CreateVideoReport::route('/create'),
            'edit' => Pages\EditVideoReport::route('/{record}/edit'),
        ];
    }
}