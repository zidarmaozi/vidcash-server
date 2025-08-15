<?php

namespace App\Filament\Resources\EventPayoutResource\Pages;

use App\Filament\Resources\EventPayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventPayout extends EditRecord
{
    protected static string $resource = EventPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
