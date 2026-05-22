<?php

namespace App\Filament\Resources\BlacklistDomainResource\Pages;

use App\Filament\Resources\BlacklistDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlacklistDomains extends ListRecords
{
    protected static string $resource = BlacklistDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
