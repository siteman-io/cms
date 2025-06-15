<?php

namespace Siteman\Cms\Resources\Roles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Siteman\Cms\Resources\Roles\RoleResource;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'used' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->whereHas('users')),
            'unused' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('users'))
                ->badge(function () {
                    $count = self::getModel()::whereDoesntHave('users')->count();

                    return $count > 0 ? $count : null;
                }),
        ];
    }
}
