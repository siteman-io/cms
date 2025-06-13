<?php
declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Siteman\Cms\Resources\MenuResource;
use Siteman\Cms\Resources\MenuResource\HasLocationAction;

class ListMenus extends ListRecords
{
    use HasLocationAction;

    public static function getResource(): string
    {
        return MenuResource::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->authorize('create_menu')
                ->label(__('siteman::menu.resource.actions.create.label'))
                ->modalHeading(__('siteman::menu.resource.actions.create.heading'))
                ->createAnother(false),
            $this->getLocationAction(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'used' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->whereHas('locations')),
            'unused' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('locations'))->badge(fn () => MenuResource::getModel()::whereDoesntHave('locations')->count()),
        ];
    }
}
