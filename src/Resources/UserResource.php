<?php

namespace Siteman\Cms\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Siteman\Cms\Resources\UserResource\Pages;

class UserResource extends Resource
{
    public static function getModel(): string
    {
        return config('siteman.models.user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('siteman::resources/user.fields.name.label')
                    ->translateLabel()
                    ->helperText(__('siteman::resources/user.fields.name.helper-text'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('email')
                    ->label('siteman::resources/user.fields.email.label')
                    ->translateLabel()
                    ->helperText(__('siteman::resources/user.fields.email.helper-text'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('siteman::resources/user.fields.password.label')
                    ->translateLabel()
                    ->helperText(__('siteman::resources/user.fields.password.helper-text'))
                    ->password()
                    ->required(),
                Select::make('roles')
                    ->label(__('siteman::resources/user.fields.roles.label'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('siteman::resources/user.table.columns.id')
                    ->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('siteman::resources/user.table.columns.name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('siteman::resources/user.table.columns.email')
                    ->translateLabel()
                    ->copyable()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('siteman::resources/user.table.actions.edit')
                    ->translateLabel(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('siteman::resources/user.table.bulk-actions.delete')
                        ->translateLabel(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return parent::getNavigationGroup() ?? __('siteman::resources/user.navigation-group');
    }

    public static function getNavigationIcon(): ?string
    {
        return parent::getNavigationIcon() ?? __('siteman::resources/user.navigation-icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::resources/user.navigation-label');
    }

    public static function getLabel(): string
    {
        return self::getNavigationLabel();
    }
}
