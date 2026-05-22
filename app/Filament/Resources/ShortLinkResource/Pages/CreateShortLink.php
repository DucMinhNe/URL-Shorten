<?php

namespace App\Filament\Resources\ShortLinkResource\Pages;

use App\Filament\Resources\ShortLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShortLink extends CreateRecord
{
    protected static string $resource = ShortLinkResource::class;
}
