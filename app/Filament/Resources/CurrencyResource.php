<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Filament\Resources\CurrencyResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Курс валюты';

    protected static ?string $modelLabel = 'Курсы валют';

    protected static ?string $pluralModelLabel = 'Курс валюты';

    protected static ?string $navigationGroup = 'Настройки';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Валюта')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('code')
                            ->label('Код')
                            ->required()
                            ->maxLength(5),
                    ]),
                    Forms\Components\Grid::make()->schema([
                        TextInput::make('convert')
                            ->required()
                            ->numeric()
                            ->label('Конвертация'),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                true => 'Активен',
                                false => 'Не активен'
                            ]),
                    ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Валюта')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('convert')
                    ->label('Конвертация')
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('status')
                    ->label('Статус')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCurrencies::route('/'),
        ];
    }
}
