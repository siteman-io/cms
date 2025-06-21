<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Siteman\Cms\Resources\Pages\Actions\CreateAction;
use Siteman\Cms\Resources\Pages\PageResource;
use Staudenmeir\LaravelAdjacencyList\Eloquent\Builder;

class PageTree extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table->modifyQueryUsing(function (Builder $query) {
            $query->isRoot()->with('descendants')->orderBy('order');
        })->reorderable('order');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false; // Disable pagination for the tree view
    }
}
