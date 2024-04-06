<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Пользователи';

    protected static ?string $modelLabel = 'Пользователя';

    protected static ?string $pluralModelLabel = 'Пользователи';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Имя'),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Фамилия'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->label('Номер телефона'),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255)
                            ->label('Адрес'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label('Почта'),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required()->maxLength(255)->label('Пароль'),
                    ])
                ]),
                Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\Select::make('country_id')
                            ->relationship('country', 'name')
                            ->required()->label('Страна'),
                        Forms\Components\Select::make('city_id')
                            ->relationship('city', 'name')
                            ->required()->label('Город'),
                    ])
                ]),
                Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')->preload()->label('Роль'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Активный',
                                'inactive' => 'Неактивный'
                            ])
                            ->default('active')->required()->label('Статус'),
                    ])
                ]),
                Forms\Components\Fieldset::make('Подписка')
                    ->relationship('userSubscription')
                    ->schema([
                        Forms\Components\Select::make('subscription')
                            ->relationship('subscription', 'name')->preload()
                            ->label('Тип'),
                        Forms\Components\TextInput::make('count_translation')
                            ->label('Кол-во переводов'),
                        Forms\Components\DatePicker::make('subscription_date')
                            ->label('Дата подписки'),
                        Forms\Components\DatePicker::make('subscription_end_date')
                            ->label('Дата окончания подписки'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Статус подписки'),
                    ])
                    ->hidden(fn ($operation): bool => in_array($operation, ['create', 'edit'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Имя'),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->label('Фамилия'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Почта'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('Номер телефона'),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->label('Адрес'),
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->sortable()
                    ->label('Страна'),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->sortable()
                    ->label('Город'),
                Tables\Columns\IconColumn::make('userSubscription.is_active')
                    ->boolean()
                    ->label('Статус подписка'),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'active' => 'Активный',
                        'inactive' => 'Неактивный'
                    ])->label('Статус'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('Moderator')) {
            return $query->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['Moderator', 'Super-Admin']);
            });
        } else {
            return $query->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['Super-Admin']);
            });
        }
    }
}
