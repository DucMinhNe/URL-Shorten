<?php

namespace App\Filament\Resources\BlacklistDomainResource\Pages;

use App\Filament\Resources\BlacklistDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlacklistDomain extends EditRecord
{
    protected static string $resource = BlacklistDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
