<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Шаблоны';

    protected static ?string $modelLabel = 'Шаблон';

    protected static ?string $pluralModelLabel = 'Шаблоны';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(200)
                            ->label('Название'),
                    ])
                ]),
                Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\Select::make('country_id')
                            ->relationship('country', 'name')
                            ->required()->label('Страна'),
                        Forms\Components\Select::make('translation_direction_id')
                            ->relationship('translationDirection', 'id')
                            ->required()->label('Языковое направление'),
                    ])
                ]),
                Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\Select::make('document_type_id')
                            ->relationship('documentType', 'name')
                            ->label('Тип документа'),
                        Forms\Components\TextInput::make('new_document_type')
                            ->maxLength(200)
                            ->label('Новый тип документа'),
                    ])
                ]),
                Card::make()->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Активный',
                                'inactive' => 'Неактивный',
                                'moderation' => 'Модерация'
                            ])
                            ->default('moderation')->required()->label('Статус'),
                    ])
                ]),
//                Forms\Components\FileUpload::make('template_file')
//                    ->disk('public')
//                    ->directory('images')
//                    ->label('Изображение')
//                    ->openable()
//                    ->downloadable()
//                    ->imageEditor()
//                    ->imageEditorViewportWidth('1920')
//                    ->imageEditorViewportHeight('1080')
//                    ->visible(fn ($record) => $record && $record->template_file),
//                Forms\Components\TextInput::make('template_file')
//                    ->required()
//                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->label('Название'),
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->sortable()->label('Страна'),
                Tables\Columns\TextColumn::make('documentType.name')
                    ->numeric()
                    ->sortable()->label('Тип документа'),
                Tables\Columns\TextColumn::make('new_document_type')
                    ->searchable()->label('Новый тип документа'),
                Tables\Columns\TextColumn::make('translationDirection.id')
                    ->numeric()
                    ->sortable()->label('Языковое направление'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Дата создания')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'active' => 'Активный',
                        'inactive' => 'Неактивный',
                        'moderation' => 'Модерация'
                    ])->label('Статус')
                    ->extraAttributes(['class' => 'custom-width']),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => config('app.front_url') . '/view?id=' . $record->id . '&user=' . auth()->user()->getAuthIdentifier() . '&token=' . config('app.admin_secret')),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'moderation')->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'view' => Pages\ViewTemplate::route('/{record}'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
