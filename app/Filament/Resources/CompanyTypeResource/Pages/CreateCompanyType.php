<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\CompanyTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyType extends CreateRecord
{
    protected static string $resource = CompanyTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
