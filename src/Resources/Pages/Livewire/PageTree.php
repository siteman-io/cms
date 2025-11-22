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
        // Load complete tree (no depth limit) with all relationships
        return Page::tree()->get()->toTree();
    }

    public function selectPage(int $pageId): void
    {
        $this->dispatch('page-selected', $pageId);
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(__('filament-actions::delete.single.label'))
            ->visible(fn (): bool => auth()->check() && auth()->user()->can('delete_page'))
            ->requiresConfirmation()
            ->modalDescription(function (array $arguments): ?string {
                if (!isset($arguments['id'])) {
                    return null;
                }

                $page = Page::query()->where('id', $arguments['id'])->first();

                if (!$page) {
                    return null;
                }

                if ($page->children()->count() > 0) {
                    return __('siteman::page.tree.delete.has_children_description');
                }

                return __('siteman::page.tree.delete.confirm_description');
            })
            ->schema(function (array $arguments): array {
                if (!isset($arguments['id'])) {
                    return [];
                }

                $page = Page::query()->where('id', $arguments['id'])->first();

                if (!$page || $page->children()->count() === 0) {
                    return [];
                }

                return [
                    \Filament\Forms\Components\Radio::make('delete_strategy')
                        ->label(__('siteman::page.tree.delete.strategy_label'))
                        ->options([
                            'cascade' => __('siteman::page.tree.delete.cascade_option'),
                            'reassign' => __('siteman::page.tree.delete.reassign_option'),
                        ])
                        ->default('cascade')
                        ->required(),
                ];
            })
            ->action(function (array $arguments, array $data): void {
                $page = Page::query()->where('id', $arguments['id'])->first();

                if (!$page) {
                    return;
                }

                $hasChildren = $page->children()->count() > 0;

                if ($hasChildren) {
                    $strategy = $data['delete_strategy'] ?? 'cascade';

                    if ($strategy === 'cascade') {
                        $page->cascadeDelete();
                    } elseif ($strategy === 'reassign') {
                        $page->deleteAndReassignChildren();
                    }
                } else {
                    $page->delete();
                }

                $this->dispatch('page:deleted', $page->id);
            });
    }

    public function reorder(array $order, ?string $parentId = null): void
    {
        if (empty($order)) {
            return;
        }

        // Process items in chunks of 200 to handle potentially large number of pages
        collect($order)->chunk(200)->each(function ($chunk, $chunkIndex) use ($parentId) {
            // Reset keys to 0-based indexing for correct order calculation
            $chunk = $chunk->values();

            // Build parameterized CASE statement for the order column
            $orderCases = $chunk
                ->map(fn ($id, $index): string => 'WHEN id = ? THEN ?')
                ->implode(' ');

            // Collect all bind values: order case pairs, parent_id, and IDs for WHERE IN
            $caseBindings = $chunk
                ->flatMap(fn ($id, $index) => [$id, ($chunkIndex * 200) + $index + 1])
                ->toArray();

            $placeholders = implode(',', array_fill(0, count($chunk), '?'));

            // Update all pages in this chunk with a single query using proper parameter binding
            DB::statement(
                "UPDATE pages SET
                    parent_id = ?,
                    \"order\" = CASE {$orderCases} ELSE \"order\" END
                WHERE id IN ({$placeholders})",
                array_merge([$parentId], $caseBindings, $chunk->toArray())
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
