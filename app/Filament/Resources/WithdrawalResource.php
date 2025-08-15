<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Kita tidak perlu form karena data dibuat oleh pengguna
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TAMBAHKAN KOLOM INI
                Tables\Columns\TextColumn::make('formatted_id')
                ->label('ID Penarikan')
                ->searchable(isIndividual: true), // Bisa dicari

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_info')
                    ->label('Info Pembayaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Diajukan')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tombol Aksi "Konfirmasi"
                Action::make('confirm')
                    ->label('Konfirmasi')
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Withdrawal $record): bool => $record->status === 'pending')
                    ->action(function (Withdrawal $record) {
                        $user = $record->user;

                        // Pastikan saldo mencukupi sebelum dipotong
                        if ($user->balance < $record->amount) {
                            Notification::make()->title('Gagal! Saldo user tidak mencukupi.')->danger()->send();
                            return;
                        }

                        // 1. Potong saldo user
                        $user->balance -= $record->amount;
                        
                        // 2. Tambahkan ke total penarikan
                        $user->total_withdrawn += $record->amount;
                        $user->save();

                        // 3. Ubah status penarikan
                        $record->status = 'confirmed';
                        $record->save();

                        // 4. Buat notifikasi untuk user
                        $user->customNotifications()->create([
                            'type' => 'withdrawal',
                            'message' => 'Permintaan penarikan Anda sebesar Rp' . number_format($record->amount) . ' telah dikonfirmasi.'
                        ]);

                        // 5. Tampilkan notifikasi untuk admin
                        Notification::make()->title('Penarikan dikonfirmasi dan saldo dipotong!')->success()->send();
                    }),

                // Tombol Aksi "Tolak"
                Action::make('reject')
                    ->label('Tolak')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Withdrawal $record): bool => $record->status === 'pending')
                    ->action(function (Withdrawal $record) {
                        // Tidak perlu kembalikan saldo karena saldo belum dipotong saat request
                        
                        // Ubah status
                        $record->status = 'rejected';
                        $record->save();
                        
                        // Buat notifikasi untuk user
                        $user = $record->user;
                        $user->customNotifications()->create([
                            'type' => 'withdrawal',
                            'message' => 'Permintaan penarikan Anda sebesar Rp' . number_format($record->amount) . ' ditolak.'
                        ]);
                        
                        Notification::make()->title('Penarikan ditolak.')->success()->send();
                    }),
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
            'index' => Pages\ListWithdrawals::route('/'),
            // Kita nonaktifkan halaman create dan edit karena tidak diperlukan
            // 'create' => Pages\CreateWithdrawal::route('/create'),
            // 'edit' => Pages\EditWithdrawal::route('/{record}/edit'),
        ];
    }    
}