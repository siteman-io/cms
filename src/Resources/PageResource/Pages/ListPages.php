<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\On;
use Siteman\Cms\Resources\PageResource;

class ListPages extends Page
{
    protected static string $resource = PageResource::class;

    protected static string $view = 'siteman::resources.page.pages.list-pages';

    public ?int $selectedPageId = null;

    #[On('page-selected')]
    public function onPageSelected(int $pageId): void
    {
        $this->selectedPageId = $pageId;
    }

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
}
