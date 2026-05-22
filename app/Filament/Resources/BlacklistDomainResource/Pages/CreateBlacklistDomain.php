<?php

namespace App\Filament\Resources\BlacklistDomainResource\Pages;

use App\Filament\Resources\BlacklistDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlacklistDomain extends CreateRecord
{
    protected static string $resource = BlacklistDomainResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
