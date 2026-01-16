<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ReferralSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Referral Settings';
    protected static ?string $title = 'Referral Settings';

    protected static string $view = 'filament.pages.referral-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = \App\Models\Setting::whereIn('key', [
            'referral_bonus_amount',
            'referee_bonus_amount',
            'referral_threshold'
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'referral_bonus_amount' => $settings['referral_bonus_amount'] ?? 0,
            'referee_bonus_amount' => $settings['referee_bonus_amount'] ?? 0,
            'referral_threshold' => $settings['referral_threshold'] ?? 10000,
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('referral_bonus_amount')
                    ->label('Bonus Referrer')
                    ->helperText('Jumlah saldo yang diterima pengundang.')
                    ->prefix('Rp')
                    ->numeric()
                    ->required(),
                \Filament\Forms\Components\TextInput::make('referee_bonus_amount')
                    ->label('Bonus Referee (Pengguna Baru)')
                    ->helperText('Jumlah saldo awal yang diterima pengguna baru saat mendaftar dengan kode.')
                    ->prefix('Rp')
                    ->numeric()
                    ->required(),
                \Filament\Forms\Components\TextInput::make('referral_threshold')
                    ->label('Threshold Saldo')
                    ->helperText('Saldo minimal yang harus dicapai referee agar referrer mendapatkan bonus.')
                    ->prefix('Rp')
                    ->numeric()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        \Filament\Notifications\Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
