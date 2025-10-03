<?php

namespace App\Filament\Resources\TelegramBroadcastVideoResource\Pages;

use App\Filament\Resources\TelegramBroadcastVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTelegramBroadcastVideo extends EditRecord
{
    protected static string $resource = TelegramBroadcastVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
