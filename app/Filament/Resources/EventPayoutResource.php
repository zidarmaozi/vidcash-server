<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventPayoutResource\Pages;
use App\Models\EventPayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class EventPayoutResource extends Resource
{
    protected static ?string $model = EventPayout::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Konfirmasi Event';
    protected static ?string $title = 'Konfirmasi Hadiah Event';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Pengguna'),
                Tables\Columns\TextColumn::make('period')->label('Periode'),
                Tables\Columns\TextColumn::make('rank')->label('Peringkat')->badge(),
                Tables\Columns\TextColumn::make('total_views')->label('Total Views')->numeric(),
                Tables\Columns\TextColumn::make('prize_amount')->label('Hadiah')->money('IDR'),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            // Tampilkan hanya yang statusnya pending secara default
            ->defaultGroup('status')
            ->bulkActions([
                BulkAction::make('confirm_payout')
                    ->label('Konfirmasi & Kirim Hadiah Terpilih')
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Collection $records) {
                        foreach ($records as $payout) {
                            if ($payout->status === 'pending') {
                                // Tambahkan saldo ke user
                                $user = $payout->user;
                                $user->balance += $payout->prize_amount;
                                $user->save();

                                // Update status payout
                                $payout->status = 'confirmed';
                                $payout->save();
                            }
                        }
                        Notification::make()->title('Hadiah berhasil dikirim!')->success()->send();
                    })
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventPayouts::route('/'),
        ];
    }    
}