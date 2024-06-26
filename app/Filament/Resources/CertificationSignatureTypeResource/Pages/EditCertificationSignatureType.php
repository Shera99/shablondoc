<?php

namespace App\Filament\Resources\CertificationSignatureTypeResource\Pages;

use App\Filament\Resources\CertificationSignatureTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificationSignatureType extends EditRecord
{
    protected static string $resource = CertificationSignatureTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Назад')
                ->url($this->getResource()::getUrl('index'))
                ->color('primary'),
//            Actions\DeleteAction::make(),
        ];
    }
}
