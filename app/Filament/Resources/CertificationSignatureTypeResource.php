<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificationSignatureTypeResource\Pages;
use App\Filament\Resources\CertificationSignatureTypeResource\RelationManagers;
use App\Models\CertificationSignatureType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CertificationSignatureTypeResource extends Resource
{
    protected static ?string $model = CertificationSignatureType::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Типы подписей';

    protected static ?string $modelLabel = 'Тип подписи';

    protected static ?string $pluralModelLabel = 'Типы подписей';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $navigationGroup = 'Справочники';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificationSignatureTypes::route('/'),
            'create' => Pages\CreateCertificationSignatureType::route('/create'),
            'edit' => Pages\EditCertificationSignatureType::route('/{record}/edit'),
        ];
    }
}
