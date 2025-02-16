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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('siteman::resources/user.table.columns.name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('siteman::resources/user.table.columns.email'))
                    ->copyable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('siteman::resources/user.table.columns.roles'))
                    ->badge()
                    ->colors([
                        'primary',
                        'success' => 'super_admin',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('siteman::resources/user.table.columns.created_at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->alignRight()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label(__('siteman::resources/user.table.filters.role.label'))
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->actions(
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->label(__('siteman::resources/user.table.actions.edit')),
                ]),
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('siteman::resources/user.table.bulk-actions.delete')),
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
