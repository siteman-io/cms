<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Size;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Siteman\Cms\Models\Page;

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
        return Page::tree(1)->get()->loadTreeRelationships()->toTree();
    }

    public function selectPage(int $pageId): void
    {
        $this->dispatch('page-selected', $pageId);
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
