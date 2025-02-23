<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\PageResource\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Collection;
use Livewire\Component;

class PageTree extends Component implements HasActions, HasForms {
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Collection $pages = null;

    public function render()
    {
        return view('siteman::resources.page-resource.livewire.page-tree');
    }
}
