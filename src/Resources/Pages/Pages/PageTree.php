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

    public ?int $selectedPageId = null;

    protected $listeners = [
        'page-selected' => 'onPageSelected',
    ];

    public function mount(): void
    {
        parent::mount();

        // Get selectedPageId from query parameter and cast to int
        $selectedPageId = request()->query('selectedPageId');
        $this->selectedPageId = $selectedPageId ? (int) $selectedPageId : null;
    }

    public function onPageSelected(int $pageId): void
    {
        $this->selectedPageId = $pageId;

        // Update URL to keep it bookmarkable
        $this->dispatch('update-url', ['selectedPageId' => $pageId]);
    }

    public function getView(): string
    {
        return 'siteman::resources.page.pages.page-tree';
    }

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
