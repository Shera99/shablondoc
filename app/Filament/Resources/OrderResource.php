<?php

namespace App\Filament\Resources;

use App\Events\NewOrder;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

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
                    Forms\Components\Select::make('translation_direction_id')
                        ->relationship('translationDirection', 'id') // Оставляем связь
                        ->options(function () {
                            return \App\Models\TranslationDirection::with(['sourceLanguage', 'targetLanguage'])
                                ->get()
                                ->mapWithKeys(function ($direction) {
                                    return [
                                        $direction->id => "{$direction->sourceLanguage->name} - {$direction->targetLanguage->name}",
                                    ];
                                });
                        })
                        ->required()
                        ->label('Языковое направление'),
                    Forms\Components\DateTimePicker::make('delivery_date')
                        ->required()
                        ->label('Дата доставки'),
                    Forms\Components\DateTimePicker::make('print_date')
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
                        ->afterStateUpdated(function (?string $state, $record) {
                            if ($state === 'completed') broadcast(new NewOrder(['type' => 'new-order']))->toOthers();
                            if ($state === 'translated') broadcast(new NewOrder(['type' => 'new-delivery', 'company_id' => $record->companyAddress->company->id]))->toOthers();
                        })
                        ->label('Статус'),
                    Forms\Components\Textarea::make('comment')
                        ->label('Комментарий'),
                    Forms\Components\TextInput::make('document_name')
                        ->label('Название документа'),
                    Forms\Components\TextInput::make('mynumer')
                        ->label('Идентификатор MyNumer')
                        ->disabled(),
                    Forms\Components\FileUpload::make('document_file')
                        ->disk('public')
                        ->directory('images')
                        ->visible(function ($record) {
                            $document_file = $record ? $record->document_file : [];
                            return $document_file;
                        })
                        ->label('Файл')
                        ->openable()
                        ->downloadable()
                        ->multiple()
                        ->visibility('public')
                        ->storeFileNamesIn('document_file')
                        ->helperText('The list of uploaded files'),
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
                Tables\Columns\TextColumn::make('translationDirection.id')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->translationDirection
                            ? $record->translationDirection->sourceLanguage->name . ' - ' . $record->translationDirection->targetLanguage->name
                            : '-';
                    })
                    ->sortable()->label('Языковое направление'),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Дата доставки'),
                Tables\Columns\TextColumn::make('print_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Дата печати'),
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
                    ->label('Статус')
                    ->extraAttributes(['class' => 'custom-width'])
                    ->afterStateUpdated(function (?string $state, $record) {
                        if ($state === 'completed') broadcast(new NewOrder(['type' => 'new-order']))->toOthers();
                        if ($state === 'translated') broadcast(new NewOrder(['type' => 'new-delivery', 'company_id' => $record->companyAddress->company->id]))->toOthers();
                    }),
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
//                Tables\Actions\DeleteBulkAction::make(),
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
        return static::getModel()::whereIn('status', ['moderation'])->count();
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
