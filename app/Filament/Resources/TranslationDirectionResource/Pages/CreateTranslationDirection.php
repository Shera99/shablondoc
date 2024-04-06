<?php

namespace App\Filament\Resources\TranslationDirectionResource\Pages;

use App\Filament\Resources\TranslationDirectionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTranslationDirection extends CreateRecord
{
    protected static string $resource = TranslationDirectionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
