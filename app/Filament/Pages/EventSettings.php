<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class EventSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static string $view = 'filament.pages.event-settings';
    protected static ?string $title = 'Pengaturan Event Bulanan';
    public ?array $data = [];

    public function mount(): void
    {
        $this->data['event_enabled'] = (bool) Setting::where('key', 'event_enabled')->first()?->value;
        $this->data['event_prize_1'] = Setting::where('key', 'event_prize_1')->first()->value ?? 75000;
        $this->data['event_prize_2'] = Setting::where('key', 'event_prize_2')->first()->value ?? 50000;
        $this->data['event_prize_3'] = Setting::where('key', 'event_prize_3')->first()->value ?? 25000;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Status Event')
                ->schema([
                    Toggle::make('event_enabled')->label('Aktifkan Event Bulan Ini'),
                ]),
            Section::make('Nominal Hadiah')
                ->schema([
                    TextInput::make('event_prize_1')->label('Hadiah Peringkat 1 (Rp)')->numeric()->required(),
                    TextInput::make('event_prize_2')->label('Hadiah Peringkat 2 (Rp)')->numeric()->required(),
                    TextInput::make('event_prize_3')->label('Hadiah Peringkat 3 (Rp)')->numeric()->required(),
                ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::updateOrCreate(['key' => 'event_enabled'], ['value' => $data['event_enabled']]);
        Setting::updateOrCreate(['key' => 'event_prize_1'], ['value' => $data['event_prize_1']]);
        Setting::updateOrCreate(['key' => 'event_prize_2'], ['value' => $data['event_prize_2']]);
        Setting::updateOrCreate(['key' => 'event_prize_3'], ['value' => $data['event_prize_3']]);

        Notification::make()->title('Pengaturan event berhasil disimpan')->success()->send();
    }
}