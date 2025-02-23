<?php

namespace Siteman\Cms\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Siteman\Cms\Resources\PageResource;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected static string $view = 'siteman::resources.page-resource.list-records';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PageResource\Widgets\HomePageWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'published' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->scopes('published')),
            'trashed' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed())->badge(fn () => self::getModel()::onlyTrashed()->count()),
        ];
    }
}
