<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

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
                Card::make()->schema([
                    Forms\Components\TextInput::make('email')
                        ->maxLength(100)
                        ->label('Почта заказчика'),
                    Forms\Components\TextInput::make('phone_number')
                        ->required()
                        ->maxLength(20)
                        ->label('Номер заказчика'),
                    Forms\Components\Select::make('company_address_id')
                        ->relationship('companyAddress', 'name')
                        ->required()
                        ->label('Адрес выдачи'),
                    Forms\Components\Select::make('template_id')
                        ->relationship('template', 'name')
                        ->label('Шаблон'),
                    Forms\Components\Select::make('country_id')
                        ->relationship('country', 'name')
                        ->label('Страна'),
                    Forms\Components\Select::make('language_id')
                        ->relationship('language', 'name')
                        ->label('Язык перевода'),
                    Forms\Components\DateTimePicker::make('delivery_date')
                        ->required()
                        ->label('Дата доставки'),
                    Forms\Components\DateTimePicker::make('print_date')
                        ->required()
                        ->label('Дата печати'),
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'В процессе оплаты',
                            'moderation' => 'Модерация',
                            'translate_moderation' => 'Модерация перевода',
                            'completed' => 'Оформлен',
                            'translated' => 'Переводен',
                            'delivery' => 'В процессе доставки',
                            'delivered' => 'Доставлено',
                            'failed' => 'Не оплачен!'
                        ])
                        ->default('pending')
                        ->required()
                        ->label('Статус'),
                    Forms\Components\Textarea::make('comment')
                        ->label('Комментарий'),
                    Forms\Components\TextInput::make('document_name')
                        ->label('Название документа'),
                    Forms\Components\FileUpload::make('document_file')
                        ->disk('public')
                        ->directory('images')
                        ->label('Файл')
                        ->openable()
                        ->downloadable()
                        ->visible(fn ($record) => $record && $record->document_file),
                ]),
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
                    ->sortable()
                    ->label('Адрес выдачи'),
                Tables\Columns\TextColumn::make('template.name')
                    ->sortable()
                    ->label('Шаблон'),
                Tables\Columns\TextColumn::make('certificationSignature.view')
                    ->sortable()
                    ->label('Заверительная подпись'),
                Tables\Columns\TextColumn::make('country.name')
                    ->sortable()
                    ->label('Страна'),
                Tables\Columns\TextColumn::make('language.name')
                    ->sortable()
                    ->label('Язык перевода'),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Дата доставки'),
                Tables\Columns\TextColumn::make('print_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Дата печати'),
                Tables\Columns\ImageColumn::make('document_file')
                    ->disk('public')
                    ->width(200)
                    ->label('Файл')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('document_name')
                    ->sortable()
                    ->label('Название документа'),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'В процессе оплаты',
                        'moderation' => 'Модерация',
                        'translate_moderation' => 'Модерация перевода',
                        'completed' => 'Оформлен',
                        'translated' => 'Переводен',
                        'delivery' => 'В процессе доставки',
                        'delivered' => 'Доставлено',
                        'failed' => 'Не оплачен!'
                    ])
                    ->label('Статус'),
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
                Tables\Actions\AssociateAction::make()
                    ->label('Проверить перевод')
                    ->url(fn ($record) => config('app.front_url') . '/translate-view?id=' . $record->id . '&user=' . auth()->user()->getAuthIdentifier() . '&token=' . config('app.admin_secret_order')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
// Add any necessary relationships here
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['moderation', 'translate_moderation'])->count();
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
