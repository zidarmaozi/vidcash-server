<?php

namespace App\Filament\Resources\EventPayoutResource\Pages;

use App\Filament\Resources\EventPayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventPayouts extends ListRecords
{
    protected static string $resource = EventPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
