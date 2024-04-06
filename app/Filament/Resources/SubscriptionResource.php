<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Подписки';

    protected static ?string $modelLabel = 'Подписку';

    protected static ?string $pluralModelLabel = 'Подписки';

    protected static ?string $navigationGroup = 'Справочники';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(200)
                    ->label('Название'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->label('Цена'),
                Forms\Components\TextInput::make('day_count')
                    ->required()
                    ->numeric()
                    ->label('Кол-во дней активности подписки'),
                Forms\Components\TextInput::make('count_translation')
                    ->required()
                    ->numeric()
                    ->label('Кол-во переводов'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->label('Статус'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Название'),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable()
                    ->label('Цена'),
                Tables\Columns\TextColumn::make('day_count')
                    ->numeric()
                    ->sortable()
                    ->label('Кол-во дней активности подписки'),
                Tables\Columns\TextColumn::make('count_translation')
                    ->numeric()
                    ->sortable()
                    ->label('Кол-во переводов'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Статус'),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
