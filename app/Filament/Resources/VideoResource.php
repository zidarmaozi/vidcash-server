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
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Dibuat')->dateTime()->sortable(),
            ])
            ->filters([
                //
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
        ];
    }    
}
