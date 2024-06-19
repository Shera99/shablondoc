<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificationSignatureResource\Pages;
use App\Filament\Resources\CertificationSignatureResource\RelationManagers;
use App\Models\CertificationSignature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CertificationSignatureResource extends Resource
{
    protected static ?string $model = CertificationSignature::class;

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    protected static ?string $navigationLabel = 'Подписи';

    protected static ?string $modelLabel = 'Подпись';

    protected static ?string $pluralModelLabel = 'Подписи';

    protected static bool $hasTitleCaseModelLabel = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->label('Страна')
                    ->required(),
                Forms\Components\Select::make('city_id')
                    ->relationship('city', 'name')
                    ->label('Город')
                    ->required(),
                Forms\Components\Select::make('company_id')
                    ->relationship('companies', 'name')
                    ->label('Компания')
                    ->required(),
                Forms\Components\Select::make('certification_signature_type_id')
                    ->relationship('certificationSignatureType', 'name')
                    ->label('Тип')
                    ->required(),
                Forms\Components\Select::make('language_id')
                    ->relationship('languages', 'name')
                    ->label('Язык')
                    ->required(),
                Forms\Components\TextInput::make('view')
                    ->label('Вид')
                    ->maxLength(255),
                Forms\Components\TextInput::make('user')
                    ->label('Пользователь')
                    ->maxLength(255),
                Forms\Components\TextInput::make('file')
                    ->label('Логотип')
                    ->maxLength(255),
                Forms\Components\Select::make('is_deleted')
                    ->label('Статус')
                    ->options([
                        true => 'Удален',
                        false => 'Активен'
                    ]),
                Forms\Components\Textarea::make('certification_text')
                    ->label('Удостоверяющаю надпись')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->label('Страна')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Город')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Компания')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('certificationSignatureType.name')
                    ->label('Тип')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language.name')
                    ->label('Язык')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('view')
                    ->label('Вид')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user')
                    ->label('Пользователь')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('file')
                    ->disk('public')
                    ->width(200)
                    ->label('Логотип')
                    ->toggleable(),
                Tables\Columns\SelectColumn::make('is_deleted')
                    ->label('Статус')
                    ->options([
                        true => 'Удален',
                        false => 'Активен'
                    ]),
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
            'index' => Pages\ListCertificationSignatures::route('/'),
            'create' => Pages\CreateCertificationSignature::route('/create'),
            'edit' => Pages\EditCertificationSignature::route('/{record}/edit'),
        ];
    }
}
