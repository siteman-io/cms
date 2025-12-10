<?php

namespace Siteman\Cms\Resources\Roles;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Siteman\Cms\Models\Role;
use Siteman\Cms\Resources\Roles\Pages\CreateRole;
use Siteman\Cms\Resources\Roles\Pages\EditRole;
use Siteman\Cms\Resources\Roles\Pages\ListRoles;
use Siteman\Cms\Resources\Roles\Schemas\RoleForm;
use Siteman\Cms\Resources\Roles\Tables\RolesTable;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|UnitEnum|null $navigationGroup = 'Admin';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function canGloballySearch(): bool
    {
        return count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }
}
