<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\On;
use Livewire\Component;
use Siteman\Cms\Models\Page;

class PageDetails extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Page $page = null;

    public function mount(?int $pageId = null): void
    {
        if ($pageId) {
            $this->loadPage($pageId);
        }
    }

    #[On('page-selected')]
    public function loadPage(int $pageId): void
    {
        $this->page = Page::findOrFail($pageId);
    }

    public function render()
    {
        return view('siteman::resources.page.livewire.page-details');
    }
}
