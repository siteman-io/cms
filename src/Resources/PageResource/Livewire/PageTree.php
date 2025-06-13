<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\PageResource\Livewire;

use Filament\Support\Enums\Size;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

    protected $listeners = [
        'page:deleted' => '$refresh',
        'page-reordered' => '$refresh',
    ];

    #[Computed]
    public function pages(): Collection
    {
        return Page::query()
            ->doesntHave('parent')
            ->withCount('children')
            ->orderBy('order')
            ->with('children', function ($query) {
                $query
                    ->whereIn('parent_id', array_unique($this->activePageIds))
                    ->orderBy('order');
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
        unset($this->pages);
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
            ->size(Size::Small)
            ->modalHeading(fn (array $arguments): string => __('filament-actions::edit.single.modal.heading', ['label' => $arguments['title']]))
            ->icon('heroicon-m-pencil-square')
            ->fillForm(fn (array $arguments): array => MenuItem::query()
                ->where('id', $arguments['id'])
                ->with('linkable')
                ->first()
                ->toArray())
            ->schema([
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
            ->modalWidth(Width::Medium)
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

    public function reorder(array $order, ?string $parentId = null): void
    {
        if (empty($order)) {
            return;
        }

        // Process items in chunks of 200 to handle potentially large number of pages
        collect($order)->chunk(200)->each(function ($chunk, $chunkIndex) use ($parentId) {
            // Build a single CASE statement for the order column
            $orderCases = collect($chunk)
                ->map(fn ($id, $index): string => "WHEN id = {$id} THEN ".(($chunkIndex * 200) + $index + 1))
                ->implode(' ');

            // Update all pages in this chunk with a single query
            // Using a raw query to properly handle the 'order' reserved keyword
            DB::statement(
                "UPDATE pages SET
                    parent_id = ?,
                    \"order\" = CASE {$orderCases} ELSE \"order\" END
                WHERE id IN (".implode(',', $chunk->toArray()).')',
                [$parentId]
            );
        });

        $this->activePageIds = array_filter($this->activePageIds, fn ($id) => $id !== (int) $parentId);

        // Remove the cached pages to force a refresh
        unset($this->pages);

        // Dispatch an event to update the UI
        $this->dispatch('page-reordered');
    }

    public function reorderAction(): Action
    {
        return Action::make('reorder')
            ->label(__('filament-forms::components.builder.actions.reorder.label'))
            ->icon('heroicon-m-arrows-up-down')
            ->color('gray')
            ->iconButton()
            ->extraAttributes(['data-sortable-handle' => true, 'class' => 'cursor-move'])
            ->livewireClickHandlerEnabled(false)
            ->size(Size::Small);
    }

    public function render()
    {
        return view('siteman::resources.page.livewire.page-tree');
    }
}
