<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AdminSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.admin-settings';
    protected static ?string $title = 'Pengaturan Aplikasi';
    public ?array $data = [];

    public function mount(): void
    {
        // Ambil data lama
        $this->data['min_withdrawal'] = Setting::where('key', 'min_withdrawal')->first()->value ?? 10000;
        $methods = Setting::where('key', 'withdrawal_methods')->first()->value ?? 'DANA,OVO,Gopay';
        $this->data['withdrawal_methods'] = explode(',', $methods);
        $amounts = Setting::where('key', 'withdrawal_amounts')->first()->value ?? '10000,25000,50000';
        $this->data['withdrawal_amounts'] = explode(',', $amounts);

        // PENGATURAN BARU
        $this->data['video_domain'] = Setting::where('key', 'video_domain')->first()->value ?? 'videy.in';
        $this->data['folder_domain'] = Setting::where('key', 'folder_domain')->first()->value ?? 'videy.in';
        $this->data['cpm'] = Setting::where('key', 'cpm')->first()->value ?? 10;
        $this->data['default_validation_level'] = Setting::where('key', 'default_validation_level')->first()->value ?? 5;
        $this->data['ip_view_limit'] = Setting::where('key', 'ip_view_limit')->first()->value ?? 2;

        // Ambil data baru
        $this->data['watch_time_seconds'] = Setting::where('key', 'watch_time_seconds')->first()->value ?? 10;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('video_domain')
                    ->label('Domain Halaman Video')
                    ->prefix('https://')
                    ->required(),
                TextInput::make('folder_domain')
                    ->label('Domain Halaman Folder')
                    ->prefix('https://')
                    ->required(),
                TextInput::make('cpm')
                    ->label('Pendapatan per View (Rp)')
                    ->numeric()->required(),
                TextInput::make('watch_time_seconds')
                    ->label('Waktu Tonton Minimum (detik)')
                    ->numeric()
                    ->required(),
                Select::make('default_validation_level')
                    ->label('Level Validasi Default')
                    ->options(array_combine(range(1, 10), range(1, 10)))
                    ->required(),
                TextInput::make('ip_view_limit')
                    ->label('Batas Maksimal View per Alamat IP')
                    ->numeric()->required(),
                TextInput::make('min_withdrawal')
                    ->label('Minimal Penarikan (IDR)')->numeric()->required(),
                TagsInput::make('withdrawal_methods')
                    ->label('Metode Penarikan yang Tersedia')->required(),
                TagsInput::make('withdrawal_amounts')
                    ->label('Pilihan Nominal Penarikan (IDR)')->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Simpan semua data ke database
        Setting::updateOrCreate(['key' => 'min_withdrawal'], ['value' => $data['min_withdrawal']]);
        Setting::updateOrCreate(['key' => 'withdrawal_methods'], ['value' => implode(',', $data['withdrawal_methods'])]);
        Setting::updateOrCreate(['key' => 'withdrawal_amounts'], ['value' => implode(',', $data['withdrawal_amounts'])]);

        // PENGATURAN BARU
        Setting::updateOrCreate(['key' => 'video_domain'], ['value' => $data['video_domain']]);
        Setting::updateOrCreate(['key' => 'folder_domain'], ['value' => $data['folder_domain']]);
        Setting::updateOrCreate(['key' => 'cpm'], ['value' => $data['cpm']]);
        Setting::updateOrCreate(['key' => 'default_validation_level'], ['value' => $data['default_validation_level']]);
        Setting::updateOrCreate(['key' => 'ip_view_limit'], ['value' => $data['ip_view_limit']]);

        // Simpan data baru
        Setting::updateOrCreate(['key' => 'watch_time_seconds'], ['value' => $data['watch_time_seconds']]);
        // Notifikasi sukses
        Notification::make()->title('Pengaturan berhasil disimpan')->success()->send();
    }
}