@php use Siteman\Cms\Models\Page; @endphp
@props(['item'])

@php
    /** @var Page $item */
@endphp

<li
    wire:key="{{ $item->getKey() }}"
    data-sortable-item="{{ $item->getKey() }}"
    x-data="{ open: {{ in_array($item->getKey(), $this->activePageIds) ? 'true' : 'false' }} }"
    @dragenter.debounce.300ms="open = true"
    @page-reordered.window="open = {{ in_array($item->getKey(), $this->activePageIds) ? 'true' : 'false' }}"
>
    <div
        class="flex px-3 py-2 fi-section transition-colors {{ $this->selectedPageId === $item->getKey() ? 'bg-primary-50 dark:bg-primary-950/30 ring-1 ring-primary-600 dark:ring-primary-400' : '' }}"
    >
        <div class="flex grow items-center gap-2">
            {{ $this->reorderAction }}
            @if($item->children->isNotEmpty())
                <x-filament::icon-button
                    icon="heroicon-o-chevron-right"
                    x-on:click.stop="open = !open"
                    x-bind:title="open ? '{{ __('siteman::page.tree.items.collapse') }}' : '{{ __('siteman::page.tree.items.expand') }}'"
                    color="gray"
                    class="transition duration-200 ease-in-out"
                    x-bind:class="{ 'rotate-90': open }"
                    size="sm"
                    data-testid="expand-toggle"
                    data-page-expand="{{ $item->getKey() }}"
                />
            @endif

            <button
                wire:click="selectPage({{ $item->getKey() }})"
                data-page-id="{{ $item->getKey() }}"
                class="flex grow gap-2 text-left transition-colors hover:text-primary-600 focus:outline-none focus:text-primary-600 dark:hover:text-primary-400 dark:focus:text-primary-400"
            >
                {{-- Page type icon --}}
                @if($item->type === 'external')
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </div>
                @else
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                @endif
                <div class="flex flex-col grow min-w-0">
                    <div class="text-xs font-medium truncate">
                        {{ $item->title }}
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                        {{ $item->slug }}
                    </div>
                </div>
            </button>
        </div>
        <div class="flex items-center gap-2">
            @if($item->published_at)
                <x-filament::badge color="success" size="xs" class="hidden md:block p-1">
                    {{ __('siteman::page.status.published') }}
                </x-filament::badge>
            @else
                <x-filament::badge color="warning" size="xs" class="hidden md:block p-1">
                    {{ __('siteman::page.status.draft') }}
                </x-filament::badge>
            @endif
            <x-filament::badge color="primary" size="xs" class="hidden sm:block p-1">
                {{ $item->type }}
            </x-filament::badge>
            @php
                $createChildAction = ($this->createChildAction)(['id' => $item->getKey()]);
                $deleteAction = ($this->deleteAction)(['id' => $item->getKey()]);
                $actions = array_filter([
                    $createChildAction->isVisible() ? $createChildAction : null,
                    $deleteAction->isVisible() ? $deleteAction : null,
                ]);
            @endphp
            @if(!empty($actions))
                <x-filament-actions::group
                    :actions="$actions"
                    data-testid="page-actions"
                    data-page-actions="{{ $item->getKey() }}"
                />
            @endif
        </div>
    </div>

    <ul
        x-collapse
        x-show="open"
        wire:key="{{ $item->getKey() }}.children"
        x-data="menuBuilder({ parentId: {{ $item->getKey()  }} })"
        class="mt-2 space-y-2 ms-4"
    >
            @foreach($item->children as $child)
                <x-siteman::page-tree-item :item="$child"/>
            @endforeach
    </ul>
</li>
