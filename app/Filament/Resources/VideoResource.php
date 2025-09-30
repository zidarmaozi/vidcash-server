<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action; // <-- Menggunakan Action biasa
use Filament\Notifications\Notification; // <-- Impor untuk notifikasi
use Illuminate\Database\Eloquent\Collection;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Select::make('user_id')->relationship('user', 'name')->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Video Aktif')
                    ->default(true)
                    ->helperText('Aktifkan atau nonaktifkan video ini'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Pemilik')->searchable(),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Tidak Aktif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('video_code')
                    ->label('Video Code')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Video code copied!')
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('thumbnail_status')
                    ->label('Thumbnail')
                    ->badge()
                    ->getStateUsing(fn (Video $record): string => $record->thumbnail_path ? 'Has Thumbnail' : 'No Thumbnail')
                    ->color(fn (Video $record): string => $record->thumbnail_path ? 'success' : 'gray')
                    ->icon(fn (Video $record): string => $record->thumbnail_path ? 'heroicon-o-photo' : 'heroicon-o-x-circle'),
                
                Tables\Columns\TextColumn::make('original_link')
                    ->label('Original Link')
                    ->limit(30)
                    ->tooltip(function (Video $record) {
                        return $record->original_link;
                    })
                    ->color('gray'),
                
                // Views and Performance Metrics
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Total Views')
                    ->counts('views')
                    ->sortable()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('income_generated')
                    ->label('Income Generated')
                    ->money('IDR')
                    ->getStateUsing(function (Video $record) {
                        return $record->views()
                            ->where('income_generated', true)
                            ->sum('income_amount');
                    })
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('validation_success_rate')
                    ->label('Success Rate')
                    ->getStateUsing(function (Video $record) {
                        $totalViews = $record->views()->count();
                        if ($totalViews === 0) return '0%';
                        
                        $successfulViews = $record->views()->where('validation_passed', true)->count();
                        $rate = round(($successfulViews / $totalViews) * 100, 1);
                        return $rate . '%';
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        $rate = (float) str_replace('%', '', $state);
                        if ($rate >= 80) return 'success';
                        if ($rate >= 60) return 'warning';
                        return 'danger';
                    }),
                
                Tables\Columns\TextColumn::make('last_view')
                    ->label('Last View')
                    ->getStateUsing(function (Video $record) {
                        $lastView = $record->views()->latest()->first();
                        return $lastView ? $lastView->created_at->diffForHumans() : 'Never';
                    })
                    ->color('gray'),
                
                // Reports column
                Tables\Columns\TextColumn::make('reports_count')
                    ->label('Reports')
                    ->counts('reports')
                    ->sortable()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('pending_reports_count')
                    ->label('Pending Reports')
                    ->getStateUsing(function (Video $record) {
                        return $record->reports()->where('status', 'pending')->count();
                    })
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success'),
                
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Dibuat')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Filter by User'),
                
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status Video')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),
                
                Tables\Filters\Filter::make('has_views')
                    ->label('Has Views')
                    ->query(fn ($query) => $query->has('views')),
                
                Tables\Filters\Filter::make('no_views')
                    ->label('No Views')
                    ->query(fn ($query) => $query->doesntHave('views')),
                
                Tables\Filters\Filter::make('has_reports')
                    ->label('Has Reports')
                    ->query(fn ($query) => $query->has('reports')),
                
                Tables\Filters\Filter::make('has_pending_reports')
                    ->label('Has Pending Reports')
                    ->query(fn ($query) => $query->whereHas('reports', fn ($q) => $q->where('status', 'pending'))),
            ])
            ->actions([
                // Copy video code action
                Action::make('copyVideoCode')
                    ->label('Salin Video Code')
                    ->icon('heroicon-o-clipboard-document')
                    ->action(null) // Aksi utama ditangani oleh JavaScript di bawah
                    ->extraAttributes(function (Video $record) {
                        $escapedCode = addslashes($record->video_code);
                        return [
                            // Menjalankan JavaScript saat tombol diklik
                            'onclick' => "
                                const textToCopy = '{$escapedCode}';
                                const textarea = document.createElement('textarea');
                                textarea.value = textToCopy;
                                document.body.appendChild(textarea);
                                textarea.select();
                                document.execCommand('copy');
                                document.body.removeChild(textarea);
                                
                                // Menampilkan notifikasi sukses dari Filament
                                new FilamentNotification()
                                    .title('Video code berhasil disalin!')
                                    .success()
                                    .send();
                            ",
                        ];
                    }),
                
                // Add view details action
                Action::make('viewDetails')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Video $record) => route('filament.admin.resources.videos.show', $record))
                    ->openUrlInNewTab(),
                
                // View reports action
                Action::make('viewReports')
                    ->label('View Reports')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->url(fn (Video $record) => route('filament.admin.resources.video-reports.index', ['tableFilters' => ['video_id' => ['value' => $record->id]]]))
                    ->openUrlInNewTab()
                    ->visible(fn (Video $record) => $record->reports()->count() > 0)
                    ->color('warning'),
                

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan Video Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Video Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin mengaktifkan semua video yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => true]);
                            });
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Video berhasil diaktifkan')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan Video Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Nonaktifkan Video Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menonaktifkan semua video yang dipilih?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => false]);
                            });
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Video berhasil dinonaktifkan')
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
            'index' => Pages\ListVideos::route('/'),
            'show' => Pages\ShowVideo::route('/{record}'),
        ];
    }    
}
