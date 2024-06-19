<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationDirectionResource\Pages;
use App\Filament\Resources\TranslationDirectionResource\RelationManagers;
use App\Models\TranslationDirection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TranslationDirectionResource extends Resource
{
    protected static ?string $model = TranslationDirection::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Языковые направления';

    protected static ?string $modelLabel = 'Языковое направление';

    protected static ?string $pluralModelLabel = 'Языковые направления';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $navigationGroup = 'Справочники';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('source_language_id')
                    ->relationship('sourceLanguage', 'name')
                    ->required()->label('Переводимый язык'),
                Forms\Components\Select::make('target_language_id')
                    ->relationship('targetLanguage', 'name')
                    ->required()->label('Язык перевода'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sourceLanguage.name')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->label('Переводимый язык'),
                Tables\Columns\TextColumn::make('targetLanguage.name')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->label('Язык перевода'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTranslationDirections::route('/'),
            'create' => Pages\CreateTranslationDirection::route('/create'),
            'edit' => Pages\EditTranslationDirection::route('/{record}/edit'),
        ];
    }
}
