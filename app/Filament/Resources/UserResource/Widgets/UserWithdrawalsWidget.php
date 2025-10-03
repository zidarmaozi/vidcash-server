<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UserWithdrawalsWidget extends BaseWidget
{
    public ?User $record = null;
    
    protected static ?string $heading = '💸 Riwayat Penarikan';
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        if (!$this->record) {
            return $table->query(\App\Models\Withdrawal::query()->where('id', 0));
        }

        return $table
            ->query($this->record->withdrawals()->latest()->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'confirmed' => 'Dikonfirmasi',
                        'pending' => 'Pending',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_account.account_name')
                    ->label('Nama Akun')
                    ->default('N/A')
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_account.account_number')
                    ->label('Nomor Rekening')
                    ->default('N/A')
                    ->copyable()
                    ->copyMessage('Nomor rekening disalin!'),

                Tables\Columns\TextColumn::make('payment_account.bank_name')
                    ->label('Bank/Provider')
                    ->default('N/A')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Permintaan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                // No view action - WithdrawalResource doesn't have view page
            ])
            ->paginated([5, 10, 25])
            ->poll('30s')
            ->emptyStateHeading('Belum ada penarikan')
            ->emptyStateDescription('User ini belum melakukan penarikan.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}

