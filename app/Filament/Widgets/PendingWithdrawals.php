<?php

namespace App\Filament\Widgets;

use App\Models\Withdrawal;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class PendingWithdrawals extends BaseWidget
{
    protected static ?string $heading = 'Permintaan Penarikan Tertunda';
    
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Withdrawal::query()->where('status', 'pending')->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Pengguna'),
                Tables\Columns\TextColumn::make('amount')->money('IDR'),
                Tables\Columns\TextColumn::make('payment_info')->label('Info Pembayaran'),
            ])
            ->actions([
                // Tombol Aksi "Konfirmasi"
                Action::make('confirm')
                    ->label('Konfirmasi')
                    ->requiresConfirmation()
                    ->color('success')
                    ->action(function (Withdrawal $record) {
                        $user = $record->user;
                        if ($user->balance < $record->amount) {
                            Notification::make()->title('Gagal! Saldo user tidak mencukupi.')->danger()->send();
                            return;
                        }
                        $user->balance -= $record->amount;
                        $user->total_withdrawn += $record->amount;
                        $user->save();

                        $record->status = 'confirmed';
                        $record->save();
                        Notification::make()->title('Penarikan dikonfirmasi!')->success()->send();
                    }),
                // Tombol Aksi "Tolak"
                Action::make('reject')
                    ->label('Tolak')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function (Withdrawal $record) {
                        $record->status = 'rejected';
                        $record->save();
                        Notification::make()->title('Penarikan ditolak.')->success()->send();
                    }),
            ]);
    }
}