<?php

namespace App\Filament\Resources\TelegramBroadcastVideoResource\Pages;

use App\Filament\Resources\TelegramBroadcastVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTelegramBroadcastVideos extends ListRecords
{
    protected static string $resource = TelegramBroadcastVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
