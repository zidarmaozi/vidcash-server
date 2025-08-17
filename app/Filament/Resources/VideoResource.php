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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Pemilik')->searchable(),
                Tables\Columns\TextColumn::make('generated_link')->label('Link Video')->searchable(),
                
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
                    ->sortable()
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
                
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Dibuat')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Filter by User'),
                
                Tables\Filters\Filter::make('has_views')
                    ->label('Has Views')
                    ->query(fn ($query) => $query->has('views')),
                
                Tables\Filters\Filter::make('no_views')
                    ->label('No Views')
                    ->query(fn ($query) => $query->doesntHave('views')),
            ])
            ->actions([
                // Ganti ClipboardAction dengan Action biasa yang menjalankan JavaScript
                Action::make('copyLink')
                    ->label('Salin Link')
                    ->icon('heroicon-o-clipboard-document')
                    ->action(null) // Aksi utama ditangani oleh JavaScript di bawah
                    ->extraAttributes(function (Video $record) {
                        // PERBAIKAN DI SINI: Menggunakan addslashes() untuk meng-escape URL
                        $escapedLink = addslashes($record->generated_link);
                        return [
                            // Menjalankan JavaScript saat tombol diklik
                            'onclick' => "
                                const textToCopy = '{$escapedLink}';
                                const textarea = document.createElement('textarea');
                                textarea.value = textToCopy;
                                document.body.appendChild(textarea);
                                textarea.select();
                                document.execCommand('copy');
                                document.body.removeChild(textarea);
                                
                                // Menampilkan notifikasi sukses dari Filament
                                new FilamentNotification()
                                    .title('Link berhasil disalin!')
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
