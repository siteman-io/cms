<?php

namespace Siteman\Cms\Resources\Users;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Resources\Users\Pages\CreateUser;
use Siteman\Cms\Resources\Users\Pages\EditUser;
use Siteman\Cms\Resources\Users\Pages\ListUsers;

class UserResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    public static function getModel(): string
    {
        return config('siteman.models.user');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if ($site = Siteman::getCurrentSite()) {
            return $query->whereHas('sites', fn (Builder $q) => $q->where('sites.id', $site->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextColumn::make('name')
                    ->label(__('siteman::user.table.columns.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('siteman::user.table.columns.email'))
                    ->copyable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label(__('siteman::user.table.columns.roles'))
                    ->badge()
                    ->colors([
                        'primary',
                        'success' => 'super_admin',
                    ]),
                TextColumn::make('created_at')
                    ->label(__('siteman::user.table.columns.created_at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->alignRight()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('siteman::user.table.filters.role.label'))
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->recordActions(
                ActionGroup::make([
                    EditAction::make()->label(__('siteman::user.table.actions.edit')),
                ]),
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('siteman::user.table.bulk-actions.delete')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
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
