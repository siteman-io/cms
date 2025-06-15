<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages\Pages;

use Filament\Resources\Pages\Page;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Siteman\Cms\Resources\HasPreviewModal;
use Siteman\Cms\Resources\Pages\Actions\CreateAction;
use Siteman\Cms\Resources\Pages\PageResource;

class ListPages extends Page
{
    use HasPreviewModal;

    protected static string $resource = PageResource::class;

    protected string $view = 'siteman::resources.page.pages.list-pages';

    #[Url]
    public ?int $selectedPageId = null;

    public function mount(): void
    {
        // Handle URL parameter for selectedPageId
        if ($this->selectedPageId) {
            // Validate that the page exists
            $page = PageResource::getModel()::find($this->selectedPageId);
            if (!$page) {
                $this->selectedPageId = null;
            }
        }
    }

    #[On('page-selected')]
    public function onPageSelected(int $pageId): void
    {
        $this->selectedPageId = $pageId;
    }

    protected function getHeaderActions(): array
    {
        //        FacadesView::share(View::PREVIEW_ACTION_SETUP_HOOK, true);

        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
