<?php

namespace App\Filament\Resources\VideoReportResource\Pages;

use App\Filament\Resources\VideoReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVideoReports extends ListRecords
{
    protected static string $resource = VideoReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
