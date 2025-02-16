<?php

namespace Siteman\Cms\Resources\RoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Siteman\Cms\Resources\RoleResource;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
