<?php

namespace App\Filament\Resources\CaptchaQuestionResource\Pages;

use App\Filament\Resources\CaptchaQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCaptchaQuestion extends CreateRecord
{
    protected static string $resource = CaptchaQuestionResource::class;
}
