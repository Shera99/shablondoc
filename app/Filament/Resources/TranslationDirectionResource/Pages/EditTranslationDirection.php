<?php

namespace App\Filament\Resources\TranslationDirectionResource\Pages;

use App\Filament\Resources\TranslationDirectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTranslationDirection extends EditRecord
{
    protected static string $resource = TranslationDirectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
