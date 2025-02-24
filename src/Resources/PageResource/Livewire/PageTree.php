<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\PageResource\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Siteman\Cms\Models\Page;

class PageTree extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    #[Computed]
    public function pages(): Collection
    {
        return Page::query()
            ->doesntHave('parent')
            ->withCount('children')
            ->get();
    }

    public function render()
    {
        return view('siteman::resources.page.livewire.page-tree');
    }
}
