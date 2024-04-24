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
                Forms\Components\TextInput::make('email')
                    ->maxLength(100)
                    ->label('Почта заказчика'),
                Forms\Components\TextInput::make('phone_number')
                    ->required()
                    ->maxLength(20)
                    ->label('Номер заказчика'),
                Forms\Components\Select::make('company_address_id')
                    ->relationship('company_addresses', 'name')
                    ->required()
                    ->label('Адрес выдачи'),
                Forms\Components\Select::make('template_id')
                    ->relationship('template', 'name')
                    ->required()
                    ->label('Шаблон'),
                Forms\Components\TextInput::make('document_file')
                    ->required()
                    ->maxLength(255)
                    ->label('Файл'),
                Forms\Components\DatePicker::make('delivery_date')
                    ->required()
                    ->label('Дата доставки'),
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
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Почта заказчика'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->sortable()
                    ->label('Номер заказчика'),
                Tables\Columns\TextColumn::make('companyAddress.name')
                    ->numeric()
                    ->sortable()
                    ->label('Адрес выдачи'),
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
