<?php

namespace App\Filament\Resources\CertificationSignatureResource\Pages;

use App\Filament\Resources\CertificationSignatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCertificationSignatures extends ListRecords
{
    protected static string $resource = CertificationSignatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
