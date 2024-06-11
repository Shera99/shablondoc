<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserSubscriptionResource\Pages;
use App\Models\UserSubscription;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserSubscriptionResource extends Resource
{
    protected static ?string $model = UserSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Подписки';

    protected static ?string $modelLabel = 'Подписка';

    protected static ?string $pluralModelLabel = 'Подписки';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'email')
                            ->label('Пользователь')
                            ->required(),
                        Forms\Components\Select::make('subscription_id')
                            ->relationship('subscription', 'name_ru')
                            ->label('Подписка')
                            ->required(),
                    ]),
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\DatePicker::make('subscription_date')
                            ->label('Дата начала подписки')
                            ->required(),
                        Forms\Components\DatePicker::make('subscription_end_date')
                            ->label('Дата окончания подписки')
                            ->required(),
                    ]),
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('count_translation')
                            ->label('Кол-во переводов')
                            ->required()
                            ->numeric(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Статус')
                            ->required(),
                    ]),
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Дата создания записи')
                            ->required(),
                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Дата обновления записи')
                            ->required(),
                    ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Пользователь')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription.name_ru')
                    ->label('Подписка')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('count_translation')
                    ->label('Кол-во переводов')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Статус')
                    ->boolean(),
                Tables\Columns\TextColumn::make('subscription_date')
                    ->label('Дата начала подписки')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription_end_date')
                    ->label('Дата окончания подписки')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания записи')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Дата обновления записи')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::join('payments as p', 'user_subscriptions.id', '=', 'p.id')
            ->where('p.type', 'subscription')->where('p.status', 'pending')
            ->where('user_subscriptions.is_active', false)->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserSubscriptions::route('/'),
            'create' => Pages\CreateUserSubscription::route('/create'),
            'view' => Pages\ViewUserSubscription::route('/{record}'),
            'edit' => Pages\EditUserSubscription::route('/{record}/edit'),
        ];
    }
}
