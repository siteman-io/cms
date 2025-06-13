<?php

namespace Siteman\Cms\Resources\UserResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Siteman\Cms\Resources\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
            'admin' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', function (Builder $query) {
                $query->where('name', 'super_admin');
            }))->badge(fn () => UserResource::getModel()::whereHas('roles', function (Builder $query) {
                $query->where('name', 'super_admin');
            })->count()),
        ];
    }
}
