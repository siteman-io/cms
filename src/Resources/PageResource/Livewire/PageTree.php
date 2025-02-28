<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\PageResource\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Siteman\Cms\Models\MenuItem;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\MenuResource\LinkTarget;

class PageTree extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public array $activePageIds = [];

    protected $listeners = ['page:deleted' => '$refresh'];

    #[Computed]
    public function pages(): Collection
    {
        return Page::query()
            ->doesntHave('parent')
            ->withCount('children')
            ->with('children', function ($query) {
                $query->whereIn('parent_id', array_unique($this->activePageIds));
            })
            ->get();
    }

    public function onPageDeleted()
    {
        unset($this->pages);
    }

    public function loadChildren(int $pageId)
    {
        $this->activePageIds[] = $pageId;
        $this->pages->find($pageId)?->load('children');
    }

    public function selectPage(int $pageId)
    {
        $this->dispatch('page-selected', $pageId);
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->label(__('filament-actions::edit.single.label'))
            ->iconButton()
            ->size(ActionSize::Small)
            ->modalHeading(fn (array $arguments): string => __('filament-actions::edit.single.modal.heading', ['label' => $arguments['title']]))
            ->icon('heroicon-m-pencil-square')
            ->fillForm(fn (array $arguments): array => MenuItem::query()
                ->where('id', $arguments['id'])
                ->with('linkable')
                ->first()
                ->toArray())
            ->form([
                TextInput::make('title')
                    ->label(__('siteman::menu.form.title'))
                    ->required(),
                TextInput::make('url')
                    ->hidden(fn (?string $state, Get $get): bool => blank($state) || filled($get('linkable_type')))
                    ->label(__('siteman::menu.form.url'))
                    ->required(),
                Placeholder::make('linkable_type')
                    ->label(__('siteman::menu.form.linkable_type'))
                    ->hidden(fn (?string $state): bool => blank($state))
                    ->content(fn (string $state) => $state),
                Placeholder::make('linkable_id')
                    ->label(__('siteman::menu.form.linkable_id'))
                    ->hidden(fn (?string $state): bool => blank($state))
                    ->content(fn (string $state) => $state),
                Select::make('target')
                    ->label(__('siteman::menu.open_in.label'))
                    ->options(LinkTarget::class)
                    ->default(LinkTarget::Self),
            ])
            ->action(
                fn (array $data, array $arguments) => MenuItem::query()
                    ->where('id', $arguments['id'])
                    ->update($data),
            )
            ->modalWidth(MaxWidth::Medium)
            ->slideOver();
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(__('filament-actions::delete.single.label'))
            ->action(function (array $arguments): void {
                $page = Page::query()->where('id', $arguments['id'])->first();

                if ($page) {
                    $this->dispatch('page:deleted', $page->id);
                    $page->delete();
                }
            });
    }

    public function render()
    {
        return view('siteman::resources.page.livewire.page-tree');
    }
}
