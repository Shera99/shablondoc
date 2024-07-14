<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?int $navigationSort = 13;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $navigationLabel = 'Прочие настройки';

    protected static ?string $modelLabel = 'Прочие настройки';

    protected static ?string $pluralModelLabel = 'Прочие настройки';

    protected static ?string $navigationGroup = 'Настройки';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->disabled()
                    ->maxLength(50)
                    ->label('Ключ поля'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(200)
                    ->label('Название поля'),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->maxLength(255)
                    ->label('Значение поля'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()->label('Ключ поля'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()->label('Название поля'),
                Tables\Columns\TextColumn::make('value')
                    ->searchable()->label('Значение поля'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Дата создания'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Дата изменения'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSettings::route('/'),
        ];
    }
}
