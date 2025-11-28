<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;

/**
 * Page Tree Livewire Component
 *
 * Renders a hierarchical tree view of pages with drag-and-drop reordering,
 * delete actions, and page selection functionality.
 *
 * @property Collection $pages Collection of root pages with descendants loaded as tree
 * @property array $activePageIds Array of page IDs that should be expanded in the tree
 * @property int|null $selectedPageId ID of the currently selected page for highlighting
 */
class PageTree extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public array $activePageIds = [];

    public ?int $selectedPageId = null;

    protected $listeners = [
        'page:deleted' => '$refresh',
        'page-reordered' => '$refresh',
        'page:updated' => '$refresh',
        'page-selected' => 'onPageSelected',
    ];

    /**
     * Handle page selection event to update highlight.
     *
     * @param  int  $pageId  The ID of the selected page
     */
    public function onPageSelected(int $pageId): void
    {
        $this->selectedPageId = $pageId;
    }

    /**
     * Get all pages as a hierarchical tree structure.
     *
     * Loads all root pages with their complete descendant tree.
     * Uses Laravel Adjacency List to efficiently build the tree structure.
     *
     * @return Collection Collection of root Page models with children loaded
     */
    #[Computed]
    public function pages(): Collection
    {
        // Load complete tree (no depth limit) with all relationships
        return Page::tree()->get()->toTree();
    }

    /**
     * Handle page selection from the tree.
     *
     * Dispatches an event to notify the parent component that a page has been selected.
     *
     * @param  int  $pageId  The ID of the selected page
     */
    public function selectPage(int $pageId): void
    {
        $this->dispatch('page-selected', $pageId);
    }

    /**
     * Configure the create child action for pages in the tree.
     *
     * Opens the create page with the parent field pre-filled.
     * Disabled at max depth (level 3) and respects create permission.
     *
     * @return Action The configured create child action
     */
    public function createChildAction(): Action
    {
        return Action::make('createChild')
            ->label(__('siteman::page.tree.actions.create_child'))
            ->icon(Heroicon::OutlinedPlusCircle)
            ->visible(fn (array $arguments): bool => auth()->user() && auth()->user()->can('create_page'))
            ->disabled(function (array $arguments): bool {
                if (!isset($arguments['id'])) {
                    return true;
                }

                $page = Page::query()
                    ->withCount('ancestors')
                    ->where('id', $arguments['id'])
                    ->first();

                if (!$page) {
                    return true;
                }

                // Depth is the number of ancestors
                // Root pages have 0 ancestors (depth 0), their children have 1 ancestor (depth 1), etc.
                $depth = $page->ancestors_count;

                // Disable if page is at max depth (level 3 = depth 2)
                return $depth >= 2;
            })
            ->schema([
                TextInput::make('title')
                    ->label(__('siteman::page.fields.title.label'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                Select::make('type')
                    ->label(__('Type'))
                    ->options(collect(Siteman::getPageTypes())->mapWithKeys(fn ($type, $key) => [$key => str($key)->headline()])->toArray())
                    ->default('page')
                    ->required(),
            ])
            ->action(function (array $arguments, array $data): void {
                if (!isset($arguments['id'])) {
                    return;
                }

                $parentPage = Page::find($arguments['id']);

                if (!$parentPage) {
                    return;
                }

                // Create the new child page
                $newPage = Page::create([
                    'title' => $data['title'],
                    'slug' => '/'.Str::slug($data['title']),
                    'type' => $data['type'],
                    'parent_id' => $parentPage->id,
                    'author_id' => auth()->id(),
                    'blocks' => [],
                ]);

                // Dispatch event to select the newly created page
                $this->dispatch('page-selected', $newPage->id);

                Notification::make()
                    ->title(__('Page created successfully'))
                    ->success()
                    ->send();
            });
    }

    /**
     * Configure the delete action for pages in the tree.
     *
     * Provides a delete action with confirmation modal that handles both simple deletion
     * (for leaf pages) and complex deletion (for pages with children, offering cascade
     * or reassign strategies).
     *
     * @return Action The configured delete action
     */
    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(__('filament-actions::delete.single.label'))
            ->visible(fn (): bool => auth()->user() && auth()->user()->can('delete_page'))
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
                Notification::make()->danger()->title('Page deleted successfully')->send();
            });
    }

    /**
     * Reorder pages via drag-and-drop.
     *
     * Updates the order and parent_id of pages in the tree. Uses database transactions
     * and chunking to handle large numbers of pages efficiently. The order array contains
     * page IDs in their new order, and parentId indicates their new parent (null for root level).
     * Requires update permission.
     *
     * @param  array<int, int>  $order  Array of page IDs in their new order
     * @param  string|null  $parentId  The new parent page ID (null for root level)
     */
    public function reorder(array $order, ?string $parentId = null): void
    {
        if (empty($order)) {
            return;
        }

        // Check permission before reordering
        if (auth()->user() && !auth()->user()->can('update_page')) {
            Notification::make()
                ->title(__('Unauthorized'))
                ->body(__('You do not have permission to reorder pages.'))
                ->danger()
                ->send();

            return;
        }

        // Handle cases where 'null' string is passed instead of actual null
        // This can happen when JavaScript sends null values
        if ($parentId === 'null' || $parentId === '0' || $parentId === 0) {
            $parentId = null;
        }

        try {
            DB::transaction(function () use ($order, $parentId) {
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
                        ->flatMap(fn ($id, $index) => [(int) $id, ($chunkIndex * 200) + $index + 1])
                        ->toArray();

                    $placeholders = implode(',', array_fill(0, count($chunk), '?'));

                    // Cast chunk IDs to integers for WHERE IN clause
                    $chunkIds = $chunk->map(fn ($id) => (int) $id)->toArray();

                    $bindings = array_merge([$parentId], $caseBindings, $chunkIds);

                    // Update all pages in this chunk with a single query using proper parameter binding
                    DB::statement(
                        "UPDATE pages SET
                            parent_id = ?,
                            \"order\" = CASE {$orderCases} ELSE \"order\" END
                        WHERE id IN ({$placeholders})",
                        $bindings
                    );
                });
            });

            $this->activePageIds = array_filter($this->activePageIds, fn ($id) => $id !== (int) $parentId);

            // Remove the cached pages to force a refresh
            unset($this->pages);

            // Dispatch an event to update the UI
            $this->dispatch('page-reordered');

            // Force Livewire to re-render the component
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            // Show error notification
            Notification::make()
                ->title(__('Error reordering pages'))
                ->body(__('An error occurred while reordering. Please try again.'))
                ->danger()
                ->send();

            // Refresh to show correct state
            unset($this->pages);
        }
    }

    /**
     * Configure the reorder action button for drag-and-drop.
     *
     * Returns an icon button that serves as the drag handle for reordering pages.
     * Hidden when user lacks update permission.
     *
     * @return Action The configured reorder action
     */
    public function reorderAction(): Action
    {
        return Action::make('reorder')
            ->label(__('filament-forms::components.builder.actions.reorder.label'))
            ->icon(Heroicon::ArrowsUpDown)
            ->color('gray')
            ->iconButton()
            ->extraAttributes(['data-sortable-handle' => true, 'class' => 'cursor-move'])
            ->livewireClickHandlerEnabled(false)
            ->size(Size::Small)
            ->visible(fn (): bool => auth()->user() && auth()->user()->can('update_page'));
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View The component view
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('siteman::resources.page.livewire.page-tree');
    }
}
