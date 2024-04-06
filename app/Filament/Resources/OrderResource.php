<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Заказы';

    protected static ?string $modelLabel = 'Заказ';

    protected static ?string $pluralModelLabel = 'Заказы';

    protected static bool $hasTitleCaseModelLabel = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()->label('Клиент'),
                Forms\Components\Select::make('company_id')
                    ->relationship('companies', 'name')
                    ->required()->label('Компания'),
                Forms\Components\Select::make('template_id')
                    ->relationship('template', 'name')
                    ->required()->label('Шаблон'),
//                Forms\Components\Select::make('template_data_id')
//                    ->relationship('templateData', 'data_json')
//                    ->required()->label('Данные документа'),
                Forms\Components\TextInput::make('document_file')
                    ->required()
                    ->maxLength(255)->label('Файл'),
                Forms\Components\DatePicker::make('delivery_date')
                    ->required()->label('Дата доставки'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'В процессе модерации',
                        'completed' => 'Оформлен',
                        'translation' => 'В процессе перевода',
                        'delivery' => 'В процессе доставки',
                        'delivered' => 'Доставлено'
                    ])
                    ->default('active')->required()->label('Статус'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->label('Клиент'),
                Tables\Columns\TextColumn::make('companies.name')
                    ->numeric()
                    ->sortable()
                    ->label('Компания'),
                Tables\Columns\TextColumn::make('template.name')
                    ->numeric()
                    ->sortable()
                    ->label('Шаблон'),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->date()
                    ->sortable()
                    ->label('Дата доставки'),
                Tables\Columns\SelectColumn::make('status')->options([
                    'pending' => 'В процессе модерации',
                    'completed' => 'Оформлен',
                    'translation' => 'В процессе перевода',
                    'delivery' => 'В процессе доставки',
                    'delivered' => 'Доставлено'
                ])->label('Статус'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Дата заказа'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Дата изменения заказа'),
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', OrderStatus::PENDING)->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
