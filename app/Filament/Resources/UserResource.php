<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Tambahkan dua baris ini
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Select::make('role')
                ->options([
                    'admin' => 'Admin',
                    'user' => 'User',
                ])
                ->required(),
            TextInput::make('password')
                ->password()
                ->required(fn (string $operation): bool => $operation === 'create') // Wajib diisi hanya saat membuat user baru
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)) // Otomatis hash password
                ->dehydrated(fn (?string $state): bool => filled($state)) // Hanya simpan jika diisi
                ->maxLength(255),

        // TAMBAHKAN FIELD INI
            Select::make('validation_level')
                ->label('Level Validasi Khusus')
                ->options(array_combine(range(1,10), range(1,10)))
                ->helperText('Kosongkan untuk menggunakan pengaturan default dari admin.'),
        ]);
}
    public static function table(Table $table): Table
    {
        return $table
             ->columns([
    Tables\Columns\TextColumn::make('name')
        ->searchable(),
    Tables\Columns\TextColumn::make('email')
        ->searchable(),
    // Tambahkan dua kolom ini
    Tables\Columns\TextColumn::make('balance')
        ->money('IDR') // Format sebagai Rupiah
        ->sortable(),
    Tables\Columns\TextColumn::make('total_withdrawn')
        ->label('Total Ditarik')
        ->money('IDR')
        ->sortable(),
    Tables\Columns\TextColumn::make('videos_count')
        ->label('Total Video')
        ->counts('videos')
        ->sortable()
        ->color('info'),
    Tables\Columns\TextColumn::make('role')
        ->badge()
        ->color(fn (string $state): string => match ($state) {
            'admin' => 'danger',
            'user' => 'success',
        }),
         // TAMBAHKAN KOLOM INI
      Tables\Columns\TextColumn::make('validation_level')
        ->label('Level Validasi')
        ->sortable()
        ->placeholder('Default'), // Tampilkan 'Default' jika nilainya kosong

    Tables\Columns\TextColumn::make('created_at')
        ->dateTime()
        ->sortable(),
])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'show' => Pages\ShowUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
