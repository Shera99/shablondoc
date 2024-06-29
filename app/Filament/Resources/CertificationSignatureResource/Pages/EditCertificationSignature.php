<?php

namespace App\Filament\Resources\CertificationSignatureResource\Pages;

use App\Filament\Resources\CertificationSignatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificationSignature extends EditRecord
{
    protected static string $resource = CertificationSignatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Назад')
                ->url($this->getResource()::getUrl('index'))
                ->color('secondary'),
//            Actions\DeleteAction::make(),
        ];
    }
}
