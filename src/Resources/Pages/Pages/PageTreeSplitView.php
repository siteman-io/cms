<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\Resources\HasPreviewModal;
use Siteman\Cms\Resources\Pages\Actions\CreateAction;
use Siteman\Cms\Resources\Pages\PageResource;

/**
 * Page Tree Split View Component
 *
 * Displays a split view with tree navigation on the left and inline edit form on the right.
 * Handles form submissions, URL state management, and event coordination between components.
 *
 * @property int|null $selectedPageId Currently selected page ID from URL or tree selection
 * @property PageModel|null $selectedPage Currently loaded page model for editing
 */
class PageTreeSplitView extends Page implements HasForms
{
    use HasPreviewModal;
    use InteractsWithForms;

    protected static string $resource = PageResource::class;

    public ?int $selectedPageId = null;

    public ?PageModel $selectedPage = null;

    public ?array $data = [];

    protected $listeners = [
        'page:deleted' => 'onPageDeleted',
        'page-reordered' => 'onPageReordered',
    ];

    /**
     * Mount the component with optional page selection from URL parameter.
     *
     * @param  int|null  $selectedPageId  Optional page ID from URL query parameter
     */
    public function mount(?int $selectedPageId = null): void
    {
        if ($selectedPageId) {
            $page = PageModel::query()
                ->with(['parent', 'children'])
                ->find($selectedPageId);

            if ($page) {
                $this->selectedPageId = $selectedPageId;
                $this->selectedPage = $page;
            }
        }

        // Fill form if we have a selected page
        if ($this->selectedPage) {
            $this->form->fill($this->selectedPage->toArray());
        }
    }

    /**
     * Handle page selection event dispatched from the tree component.
     *
     * Loads the selected page, fills the form, and dispatches URL update event.
     *
     * @param  int  $pageId  The ID of the page to select and load
     */
    #[On('page-selected')]
    public function onPageSelected(int $pageId): void
    {
        $this->loadPage($pageId);

        // Dispatch event to update URL without page reload
        $this->dispatch('update-url', selectedPageId: $pageId);
    }

    /**
     * Load a page into the form for editing.
     *
     * @param  int  $pageId  The ID of the page to load
     */
    protected function loadPage(int $pageId): void
    {
        $page = PageModel::query()
            ->with(['parent', 'children'])
            ->find($pageId);

        if (!$page) {
            Notification::make()
                ->title(__('siteman::page.notifications.not_found'))
                ->danger()
                ->send();

            $this->selectedPageId = null;
            $this->selectedPage = null;
            $this->data = [];

            return;
        }

        $this->selectedPageId = $pageId;
        $this->selectedPage = $page;

        // Fill form with page data
        $this->form->fill($page->toArray());
    }

    /**
     * Handle page deletion event from tree component.
     *
     * Clears the form if the deleted page was currently selected.
     *
     * @param  int  $pageId  The ID of the deleted page
     */
    public function onPageDeleted(int $pageId): void
    {
        // If the deleted page was selected, clear the selection
        if ($this->selectedPageId === $pageId) {
            $this->selectedPageId = null;
            $this->selectedPage = null;
            $this->data = [];

            // Update URL to remove selectedPageId
            $this->dispatch('update-url', selectedPageId: null);
        }
    }

    /**
     * Handle page reordering event from tree component.
     *
     * Refreshes the tree display while maintaining current selection.
     */
    public function onPageReordered(): void
    {
        // Tree will refresh automatically via its own listener
        // We just need to ensure our form stays intact
    }

    /**
     * Define the form schema for editing pages.
     *
     * Uses the PageResource form schema to ensure consistency.
     * Sets the form record to the selected page for proper relationship handling.
     * Disables all fields if user lacks update permission.
     *
     * @param  Schema  $schema  The schema builder instance
     * @return Schema The configured form schema
     */
    public function form(Schema $schema): Schema
    {
        $schema = PageResource::form($schema)
            ->statePath('data');

        // Set the model/record for relationship fields
        if ($this->selectedPage) {
            $schema->record($this->selectedPage);

            // Disable all form fields if user lacks update permission
            $canUpdate = auth()->check() && auth()->user()->can('update', $this->selectedPage);
            $schema->disabled(!$canUpdate);
        } else {
            $schema->model(PageModel::class);
        }

        return $schema;
    }

    /**
     * Save the form and update the selected page record.
     *
     * Validates form data, updates the page, shows notification, and refreshes the tree.
     * Checks update permission before saving.
     */
    public function save(): void
    {
        if (!$this->selectedPage) {
            return;
        }

        // Check permission before saving
        if (!auth()->user()->can('update', $this->selectedPage)) {
            Notification::make()
                ->title(__('Unauthorized'))
                ->body(__('You do not have permission to update this page.'))
                ->danger()
                ->send();

            return;
        }

        try {
            // Validate and get form data
            $data = $this->form->getState();

            DB::transaction(function () use ($data) {
                // Update the page
                $this->selectedPage->fill($data);
                $this->selectedPage->save();
            });

            // Show success notification
            Notification::make()
                ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                ->success()
                ->send();

            // Dispatch event to refresh tree display
            $this->dispatch('page:updated', $this->selectedPage->id);

            // Reload the page to reflect any computed changes (like computed_slug)
            $this->loadPage($this->selectedPage->id);

        } catch (\Filament\Support\Exceptions\Halt $e) {
            // Let Filament's Halt exception bubble up for validation errors
            throw $e;
        } catch (\InvalidArgumentException $e) {
            // Handle validation errors (depth limit, circular reference, etc.)
            Notification::make()
                ->title(__('filament-panels::resources/pages/edit-record.notifications.error'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            // Reload to reset form to last valid state
            $this->loadPage($this->selectedPage->id);

        } catch (\Exception $e) {
            // Handle unexpected errors
            Notification::make()
                ->title(__('filament-panels::resources/pages/edit-record.notifications.error'))
                ->body(__('An unexpected error occurred while saving.'))
                ->danger()
                ->send();

            // Log error for debugging
            logger()->error('PageTreeSplitView save error', [
                'page_id' => $this->selectedPage->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Reload to reset form
            $this->loadPage($this->selectedPage->id);
        }
    }

    /**
     * Cancel editing and reset the form to the original page state.
     */
    public function cancel(): void
    {
        if ($this->selectedPage) {
            $this->loadPage($this->selectedPage->id);

            Notification::make()
                ->title(__('Changes discarded'))
                ->body(__('The form has been reset to the saved state.'))
                ->info()
                ->send();
        }
    }

    /**
     * Get the header actions for the page.
     *
     * Shows Create button only if user has create permission.
     *
     * @return array<Action> Array of header actions (Create button)
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => auth()->user()->can('create', PageModel::class)),
        ];
    }

    /**
     * Get the form actions (Save, Cancel, Delete buttons).
     *
     * Buttons are hidden or disabled based on user permissions.
     *
     * @return array<Action> Array of form actions
     */
    protected function getFormActions(): array
    {
        $canUpdate = $this->selectedPage
            && auth()->user()->can('update', $this->selectedPage);

        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save')
                ->keyBindings(['mod+s'])
                ->visible(fn (): bool => $canUpdate),

            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
                ->action('cancel')
                ->color('gray')
                ->visible(fn (): bool => $canUpdate),

            DeleteAction::make()
                ->record(fn (): ?Model => $this->selectedPage)
                ->after(fn () => $this->onPageDeleted($this->selectedPage->id))
                ->color('gray')
                ->visible(fn (): bool => $this->selectedPage
                    && auth()->user()->can('delete', $this->selectedPage)),
        ];
    }

    /**
     * Get the title for the page header.
     *
     * @return string The page title
     */
    public function getTitle(): string
    {
        return __('siteman::page.tree.title');
    }

    /**
     * Check if the form is read-only due to lack of permissions.
     *
     * @return bool True if form should be read-only
     */
    public function isFormReadOnly(): bool
    {
        if (!$this->selectedPage) {
            return false;
        }

        return !auth()->user()->can('update', $this->selectedPage);
    }

    /**
     * Get breadcrumbs for the selected page.
     *
     * Builds a hierarchical breadcrumb trail showing the page's location in the tree.
     * Returns an array with URLs as keys and page titles as values for Filament's breadcrumbs component.
     *
     * @return array<string, string> Array of URLs => titles from root to current page
     */
    public function getBreadcrumbs(): array
    {
        $selectedPage = $this->selectedPage;

        if (!$selectedPage) {
            return [
                static::getUrl() => static::getTitle(),
            ];
        }

        $breadcrumbs = [
            static::getUrl() => static::getTitle(),
        ];

        // Collect all ancestors
        $ancestors = [];
        $page = $selectedPage;

        while ($page) {
            array_unshift($ancestors, $page);
            $page = $page->parent;
        }

        // Build breadcrumb trail with clickable URLs
        foreach ($ancestors as $ancestor) {
            $url = static::getUrl(['selectedPageId' => $ancestor->id]);
            $breadcrumbs[$url] = $ancestor->title;
        }

        return $breadcrumbs;
    }

    /**
     * Get the view for the page.
     *
     * @return string The view name
     */
    public function getView(): string
    {
        return 'siteman::resources.page.pages.page-tree-split-view';
    }
}
