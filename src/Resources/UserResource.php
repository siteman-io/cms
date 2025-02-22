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
    protected static ?string $recordTitleAttribute = 'name';

    public static function getModel(): string
    {
        return config('siteman.models.user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('siteman::user.fields.name.label')
                    ->translateLabel()
                    ->helperText(__('siteman::user.fields.name.helper-text'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('email')
                    ->label('siteman::user.fields.email.label')
                    ->translateLabel()
                    ->helperText(__('siteman::user.fields.email.helper-text'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('roles')
                    ->label(__('siteman::user.fields.roles.label'))
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
                    ->label(__('siteman::user.table.columns.name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('siteman::user.table.columns.email'))
                    ->copyable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('siteman::user.table.columns.roles'))
                    ->badge()
                    ->colors([
                        'primary',
                        'success' => 'super_admin',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('siteman::user.table.columns.created_at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->alignRight()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label(__('siteman::user.table.filters.role.label'))
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->actions(
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->label(__('siteman::user.table.actions.edit')),
                ]),
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('siteman::user.table.bulk-actions.delete')),
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
        return parent::getNavigationGroup() ?? __('siteman::user.navigation.group');
    }

    public static function getNavigationIcon(): ?string
    {
        return parent::getNavigationIcon() ?? __('siteman::user.navigation.icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::user.plural-label');
    }

    public static function getLabel(): string
    {
        return __('siteman::user.label');
    }
}
