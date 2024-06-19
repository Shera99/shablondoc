<?php

namespace App\Filament\Resources\CertificationSignatureTypeResource\Pages;

use App\Filament\Resources\CertificationSignatureTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCertificationSignatureTypes extends ListRecords
{
    protected static string $resource = CertificationSignatureTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
