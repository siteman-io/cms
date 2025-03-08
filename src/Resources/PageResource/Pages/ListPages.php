<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\View as FacadesView;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Pboivin\FilamentPeek\Support\View;
use Siteman\Cms\Resources\HasPreviewModal;
use Siteman\Cms\Resources\PageResource;

class ListPages extends Page
{
    use HasPreviewModal;
    protected static string $resource = PageResource::class;

    protected static string $view = 'siteman::resources.page.pages.list-pages';

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
        FacadesView::share(View::PREVIEW_ACTION_SETUP_HOOK, true);

        return [
            Actions\CreateAction::make()->model(PageResource::getModel()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PageResource\Widgets\HomePageWidget::class,
        ];
    }
}
