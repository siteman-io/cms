<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\PageResource\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Siteman\Cms\Models\Page;

class PageTreeItem extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

//    protected $listeners = ['page:deleted' => '$refresh'];

    public Page $item;

    public ?Collection $children = null;

    public bool $childrenLoaded = false;

    #[Computed]
    public function hasChildren(): bool
    {
        return $this->item->children_count > 0 || $this->children?->isNotEmpty();
    }

    public function loadChildren(): void
    {
        $this->children = $this->item->children()->withCount('children')->get();
        $this->childrenLoaded = true;
        $this->selectPage();
    }

    public function selectPage(): void
    {
        $this->dispatch('page-selected', pageId: $this->item->id);
    }

    public function resetChildrenLoaded(): void
    {
        $this->childrenLoaded = false;
    }

    public function render()
    {
        return view('siteman::resources.page.livewire.page-tree-item');
    }

    public function deleteAction()
    {
        return Action::make('delete')
            ->color('gray')
            ->action(function () {
                $this->item->delete();
                $this->dispatch('page:deleted');
            })
            ->size(ActionSize::Small);
    }
}
