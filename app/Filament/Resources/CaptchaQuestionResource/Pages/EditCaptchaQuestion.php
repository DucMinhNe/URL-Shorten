<?php

namespace App\Filament\Resources\CaptchaQuestionResource\Pages;

use App\Filament\Resources\CaptchaQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCaptchaQuestion extends EditRecord
{
    protected static string $resource = CaptchaQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
