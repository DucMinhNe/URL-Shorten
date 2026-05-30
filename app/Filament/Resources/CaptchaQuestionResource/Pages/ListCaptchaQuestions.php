<?php

namespace App\Filament\Resources\CaptchaQuestionResource\Pages;

use App\Filament\Resources\CaptchaQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCaptchaQuestions extends ListRecords
{
    protected static string $resource = CaptchaQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
