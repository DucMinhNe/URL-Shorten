<?php

namespace App\Filament\Resources\ReportedLinkResource\Pages;

use App\Filament\Resources\ReportedLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportedLinks extends ListRecords
{
    protected static string $resource = ReportedLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
