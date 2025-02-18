<?php

namespace Siteman\Cms\Resources\PostResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Siteman\Cms\Resources\PostResource;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
