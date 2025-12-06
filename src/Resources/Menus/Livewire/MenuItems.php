<?php
declare(strict_types=1);

namespace Siteman\Cms\Resources\Menus\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\MenuItem;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Menus\LinkTarget;
use Siteman\Cms\Resources\Pages\PageResource;

class MenuItems extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Menu $menu;

    #[Computed]
    #[On('menu:created')]
    public function menuItems(): Collection
    {
        return $this->menu->menuItems;
    }

    public function reorder(array $order, ?string $parentId = null): void
    {
        if (empty($order)) {
            return;
        }

        MenuItem::query()
            ->whereIn('id', $order)
            ->update([
                'order' => DB::raw(
                    'case '.collect($order)
                        ->map(fn ($recordKey, int $recordIndex): string => 'when id = '.DB::getPdo()->quote($recordKey).' then '.($recordIndex + 1))
                        ->implode(' ').' end',
                ),
                'parent_id' => $parentId,
            ]);
    }

    public function reorderAction(): Action
    {
        return Action::make('reorder')
            ->label(__('filament-forms::components.builder.actions.reorder.label'))
            ->icon(FilamentIcon::resolve('forms::components.builder.actions.reorder') ?? 'heroicon-m-arrows-up-down')
            ->color('gray')
            ->iconButton()
            ->extraAttributes(['data-sortable-handle' => true, 'class' => 'cursor-move'])
            ->livewireClickHandlerEnabled(false)
            ->visible(fn (): bool => auth()->user()?->can('update_menu') ?? false)
            ->size(Size::Small);
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->label(__('filament-actions::edit.single.label'))
            ->size(Size::Small)
            ->visible(fn (): bool => auth()->user()?->can('update_menu') ?? false)
            ->modalHeading(fn (array $arguments): string => __('filament-actions::edit.single.modal.heading', ['label' => $arguments['title']]))
            ->icon('heroicon-m-pencil-square')
            ->fillForm(function (array $arguments): array {
                $menuItem = MenuItem::query()
                    ->where('id', $arguments['id'])
                    ->with('linkable')
                    ->first();

                return [
                    'title' => $menuItem->title,
                    'url' => $menuItem->getAttributes()['url'] ?? null,
                    'target' => $menuItem->target,
                    'linkable_type' => $menuItem->linkable_type,
                    'linkable_id' => $menuItem->linkable_id,
                    'includeChildren' => $menuItem->include_children,
                ];
            })
            ->schema([
                TextInput::make('title')
                    ->label(__('siteman::menu.form.title'))
                    ->required(),
                TextInput::make('url')
                    ->label(__('siteman::menu.form.url'))
                    ->visible(fn (Get $get): bool => blank($get('linkable_type'))),
                Text::make(fn (Get $get): ?HtmlString => $this->getPageLink($get('linkable_id')))
                    ->visible(fn (Get $get): bool => filled($get('linkable_type'))),
                Select::make('target')
                    ->label(__('siteman::menu.open_in.label'))
                    ->options(LinkTarget::class)
                    ->default(LinkTarget::Self),
                Toggle::make('includeChildren')
                    ->label(__('siteman::menu.form.include_children'))
                    ->visible(fn (Get $get): bool => $this->pageHasChildren($get('linkable_type'), $get('linkable_id')))
                    ->dehydrated(),
            ])
            ->action(function (array $data, array $arguments): void {
                $menuItem = MenuItem::query()->where('id', $arguments['id'])->first();

                $menuItem->update([
                    'title' => $data['title'],
                    'url' => $data['url'] ?? null,
                    'target' => $data['target'],
                    'meta' => array_merge($menuItem->meta?->toArray() ?? [], [
                        'include_children' => $data['includeChildren'] ?? false,
                    ]),
                ]);
            })
            ->modalWidth(Width::Medium)
            ->slideOver();
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(__('filament-actions::delete.single.label'))
            ->color('danger')
            ->visible(fn (): bool => auth()->user()?->can('update_menu') ?? false)
            ->groupedIcon(FilamentIcon::resolve('actions::delete-action.grouped') ?? 'heroicon-m-trash')
            ->icon('heroicon-s-trash')
            ->size(Size::Small)
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments): string => __('filament-actions::delete.single.modal.heading', ['label' => $arguments['title']]))
            ->modalSubmitActionLabel(__('filament-actions::delete.single.modal.actions.delete.label'))
            ->modalIcon(FilamentIcon::resolve('actions::delete-action.modal') ?? 'heroicon-o-trash')
            ->action(function (array $arguments): void {
                $menuItem = MenuItem::query()->where('id', $arguments['id'])->first();

                $menuItem?->delete();
            });
    }

    public function createChildAction(): Action
    {
        return Action::make('createChild')
            ->label(__('siteman::menu.resource.actions.create_child.label'))
            ->icon(Heroicon::OutlinedPlusCircle)
            ->visible(fn (): bool => auth()->user()?->can('update_menu') ?? false)
            ->modalHeading(__('siteman::menu.resource.actions.create_child.heading'))
            ->schema([
                Select::make('type')
                    ->label(__('siteman::menu.resource.actions.create_child.type'))
                    ->options([
                        'page_link' => __('siteman::menu.page_link'),
                        'custom_link' => __('siteman::menu.custom_link'),
                        'custom_text' => __('siteman::menu.custom_text'),
                    ])
                    ->default('page_link')
                    ->live()
                    ->required(),
                Select::make('pageId')
                    ->label(__('siteman::menu.resource.actions.create_child.page'))
                    ->searchable()
                    ->live()
                    ->getSearchResultsUsing(fn (string $search): array => Page::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Page::find($value)?->title)
                    ->options(Page::published()->latest()->limit(10)->pluck('title', 'id'))
                    ->visible(fn (Get $get): bool => $get('type') === 'page_link')
                    ->required(fn (Get $get): bool => $get('type') === 'page_link'),
                Toggle::make('includeChildren')
                    ->label(__('siteman::menu.form.include_children'))
                    ->visible(fn (Get $get): bool => $get('type') === 'page_link' && $this->selectedPageHasChildren($get('pageId'))),
                TextInput::make('title')
                    ->label(__('siteman::menu.form.title'))
                    ->visible(fn (Get $get): bool => $get('type') !== 'page_link')
                    ->required(fn (Get $get): bool => $get('type') !== 'page_link'),
                TextInput::make('url')
                    ->label(__('siteman::menu.form.url'))
                    ->visible(fn (Get $get): bool => $get('type') === 'custom_link')
                    ->required(fn (Get $get): bool => $get('type') === 'custom_link'),
                Select::make('target')
                    ->label(__('siteman::menu.open_in.label'))
                    ->options(LinkTarget::class)
                    ->default(LinkTarget::Self)
                    ->visible(fn (Get $get): bool => $get('type') !== 'custom_text'),
            ])
            ->action(function (array $data, array $arguments): void {
                $parentId = $arguments['id'];
                $parent = MenuItem::find($parentId);

                if (!$parent) {
                    return;
                }

                $maxOrder = MenuItem::where('parent_id', $parentId)->max('order') ?? 0;

                $itemData = [
                    'menu_id' => $this->menu->id,
                    'parent_id' => $parentId,
                    'order' => $maxOrder + 1,
                ];

                if ($data['type'] === 'page_link') {
                    $page = Page::find($data['pageId']);
                    if (!$page) {
                        return;
                    }
                    $itemData['title'] = $page->title;
                    $itemData['linkable_type'] = Page::class;
                    $itemData['linkable_id'] = $page->id;
                    $itemData['target'] = $data['target'] ?? LinkTarget::Self->value;
                    $itemData['meta'] = ['include_children' => $data['includeChildren'] ?? false];
                } elseif ($data['type'] === 'custom_link') {
                    $itemData['title'] = $data['title'];
                    $itemData['url'] = $data['url'];
                    $itemData['target'] = $data['target'] ?? LinkTarget::Self->value;
                } else {
                    // Custom text - no target needed
                    $itemData['title'] = $data['title'];
                }

                MenuItem::create($itemData);

                $this->dispatch('menu:created');

                Notification::make()
                    ->title(__('siteman::menu.notifications.created.title'))
                    ->success()
                    ->send();
            })
            ->modalWidth(Width::Medium)
            ->slideOver();
    }

    protected function selectedPageHasChildren(?int $pageId): bool
    {
        if ($pageId === null) {
            return false;
        }

        return Page::where('parent_id', $pageId)->exists();
    }

    protected function pageHasChildren(?string $linkableType, ?int $linkableId): bool
    {
        if ($linkableType !== Page::class || $linkableId === null) {
            return false;
        }

        return Page::where('parent_id', $linkableId)->exists();
    }

    protected function getPageLink(?int $linkableId): ?HtmlString
    {
        if ($linkableId === null) {
            return null;
        }

        $page = Page::find($linkableId);

        if (!$page) {
            return null;
        }

        $url = PageResource::getUrl('edit', ['record' => $page]);
        $label = __('siteman::menu.form.linked_page');

        return new HtmlString(
            sprintf(
                '<a href="%s" target="_blank" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 underline">%s: %s</a>',
                e($url),
                e($label),
                e($page->title),
            )
        );
    }

    public function render(): View
    {
        return view('siteman::resources.menu.livewire.menu-items');
    }
}
