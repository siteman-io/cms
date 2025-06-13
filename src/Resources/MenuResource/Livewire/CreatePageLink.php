<?php

declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource\Livewire;

use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\MenuResource\LinkTarget;

class CreatePageLink extends Component implements HasForms
{
    use InteractsWithForms;

    public Menu $menu;

    public ?int $pageId = null;

    public function save(): void
    {
        $this->validate([
            'pageId' => ['required', 'exists:pages,id'],
        ]);

        $page = Page::findOrFail($this->pageId);

        $this->menu
            ->menuItems()
            ->create([
                'title' => $page->title,
                'linkable_type' => Page::class,
                'linkable_id' => $page->id,
                'target' => LinkTarget::Self->value,
                'order' => $this->menu->menuItems->max('order') + 1,
            ]);

        Notification::make()
            ->title(__('siteman::menu.notifications.created.title'))
            ->success()
            ->send();

        $this->reset('pageId');
        $this->dispatch('menu:created');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pageId')
                    ->hiddenLabel()
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Page::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Page::find($value)?->title)
                    ->options(Page::published()->latest()->limit(10)->pluck('title', 'id')),
            ]);
    }

    public function render(): View
    {
        return view('siteman::resources.menu.livewire.create-page-link');
    }
}
