<?php declare(strict_types=1);

namespace Siteman\Cms\Resources;

use BezhanSalleh\FilamentShield\Resources\RoleResource as FilamentShieldRoleResource;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\CreateRole;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\EditRole;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\ViewRole;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Siteman\Cms\Resources\RoleResource\Pages\ListRoles;

class RoleResource extends FilamentShieldRoleResource
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('font-medium')
                    ->label(__('siteman::resources/role.table.columns.name'))
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->alignCenter()
                    ->color('warning')
                    ->label(__('siteman::resources/role.table.columns.guard_name')),
                Tables\Columns\TextColumn::make('team.name')
                    ->default('Global')
                    ->badge()
                    ->color(fn (mixed $state): string => str($state)->contains('Global') ? 'gray' : 'primary')
                    ->label(__('filament-shield::filament-shield.column.team'))
                    ->searchable()
                    ->visible(fn (): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled()),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('siteman::resources/role.table.columns.permissions_count'))
                    ->counts('permissions')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('users_count')
                    ->badge()
                    ->label(__('siteman::resources/role.table.columns.users_count'))
                    ->counts('users')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('siteman::resources/role.table.columns.updated_at'))
                    ->alignRight()
                    ->dateTimeTooltip()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions(Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->color('gray'),
            ]))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('siteman::resources/role.navigation-group');
    }

    public static function getNavigationIcon(): string
    {
        return __('siteman::resources/role.navigation-icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::resources/role.navigation-label');
    }

    public static function getLabel(): string
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationBadge(): null
    {
        return null;
    }
}
