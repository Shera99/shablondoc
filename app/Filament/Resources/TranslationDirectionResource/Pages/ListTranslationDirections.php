<?php

namespace App\Filament\Resources\TranslationDirectionResource\Pages;

use App\Filament\Resources\TranslationDirectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTranslationDirections extends ListRecords
{
    protected static string $resource = TranslationDirectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
