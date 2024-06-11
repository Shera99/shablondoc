<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Order;
use App\Models\UserSubscription;
use Filament\Forms\Form;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Платежи';

    protected static ?string $modelLabel = 'Платеж';

    protected static ?string $pluralModelLabel = 'Платежи';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
//                    TextInput::make('user_id')
//                        ->numeric()
//                        ->label('User ID'),
                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->label('Сумма'),
                    TextInput::make('transaction_id')
                        ->required()
                        ->label('Номер транзакции'),
//                    TextInput::make('additional_transaction_id')
//                        ->label('Additional Transaction ID'),
                    Select::make('type')
                        ->options([
                            'order' => 'Заказ',
                            'subscription' => 'Подписка',
                        ])
                        ->required()
                        ->label('Статус'),
//                    Select::make('foreign_id')
//                        ->label('Заказ/Подписка')
//                        ->options(function (callable $get) {
//                            $type = $get('type');
//                            if ($type == 'subscription') {
//                                return UserSubscription::all()->pluck('name', 'id'); // Adjust 'name' to the appropriate field
//                            } else {
//                                return Order::all()->pluck('name', 'id'); // Adjust 'name' to the appropriate field
//                            }
//                        })
//                        ->searchable(),
//                    TextInput::make('payload')
//                        ->label('Payload')
//                        ->json(),
                    Select::make('status')
                        ->options([
                            'pending' => 'В процессе',
                            'completed' => 'Успешно',
                            'failed' => 'Ошибка',
                            'refunded' => 'Возврат'
                        ])
                        ->required()
                        ->label('Статус'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
//                Tables\Columns\TextColumn::make('user_id')
//                    ->label('User ID')
//                    ->sortable()
//                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Номер транзакции')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\SelectColumn::make('type')
                    ->label('Тип')
                    ->options([
                        'order' => 'Заказ',
                        'subscription' => 'Подписка',
                    ])
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('foreign_id')
                    ->label('Заказ/Подписка')
                    ->formatStateUsing(function ($record) {
                        if ($record->type == 'subscription') {
                            $subscription = UserSubscription::find($record->foreign_id);
                            return $subscription ? "<a style='color: #3f6212' href='/shablondoc/user-subscriptions/{$subscription->id}'>Перейти к подписке</a>" : 'Подписка не найдена';
                        } else {
                            $order = Order::find($record->foreign_id);
                            return $order ? "<a style='color: #3f6212' href='/shablondoc/orders/{$order->id}'>Перейти в заказ</a>" : 'Заказ не найден';
                        }
                    })
                    ->html()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'В процессе',
                        'completed' => 'Успешно',
                        'failed' => 'Ошибка',
                        'refunded' => 'Возврат'
                    ])
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Дата обновление')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // Add any necessary filters here
            ])
            ->actions([
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add any necessary relationships here
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
//            'view' => Pages\ViewPayment::route('/{record}'),
//            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
