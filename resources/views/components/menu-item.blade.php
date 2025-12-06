@php use Siteman\Cms\Models\MenuItem; use Siteman\Cms\Models\Page; @endphp
@props(['item'])

@php
    /** @var MenuItem $item */
    $hasMenuItemChildren = $item->menuItemChildren->isNotEmpty();
    $isPageLink = $item->linkable_type !== null;
    $isCustomLink = $item->linkable_type === null && $item->url !== null;
    $isCustomText = $item->linkable_type === null && $item->url === null;
    $hasPageChildren = $item->include_children && $item->linkable instanceof Page && $item->linkable->children->isNotEmpty();
    $hasAnyChildren = $hasMenuItemChildren || $hasPageChildren;
@endphp

<li
    wire:key="{{ $item->getKey() }}"
    data-sortable-item="{{ $item->getKey() }}"
    x-data="{ open: $persist({{ $hasAnyChildren ? 'true' : 'false' }}).as('menu-item-' + {{ $item->getKey() }}) }"
>
    <div class="flex px-3 py-2 fi-section transition-colors">
        <div class="flex grow items-center gap-2">
            {{ $this->reorderAction }}

            <x-filament::icon-button
                icon="heroicon-o-chevron-right"
                x-on:click.stop="open = !open"
                x-bind:title="open ? '{{ trans('siteman::menu.items.collapse') }}' : '{{ trans('siteman::menu.items.expand') }}'"
                color="gray"
                class="transition duration-200 ease-in-out"
                x-bind:class="{ 'rotate-90': open }"
                size="sm"
            />

            {{-- Type icon --}}
            <div class="flex-shrink-0 mt-0.5">
                @if($isPageLink)
                    <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                @elseif($isCustomLink)
                    <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                @else
                    <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                @endif
            </div>

            {{-- Two-line content --}}
            <div class="flex flex-col grow min-w-0">
                <div class="text-xs font-medium truncate text-gray-950 dark:text-white">
                    {{ $item->title }}
                </div>
                @if($item->url)
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                        {{ $item->url }}
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-filament::badge color="gray" size="xs" class="hidden sm:block p-1">
                {{ $item->type }}
            </x-filament::badge>

            @php
                $editAction = ($this->editAction)(['id' => $item->getKey(), 'title' => $item->title]);
                $deleteAction = ($this->deleteAction)(['id' => $item->getKey(), 'title' => $item->title]);
                $actions = array_filter([
                    $editAction->isVisible() ? $editAction : null,
                    $deleteAction->isVisible() ? $deleteAction : null,
                ]);
            @endphp

            @if(!empty($actions))
                <x-filament-actions::group :actions="$actions" />
            @endif
        </div>
    </div>

    {{-- Always render the nested ul for drag-and-drop nesting to work --}}
    <ul
        x-collapse
        x-show="open"
        wire:key="{{ $item->getKey() }}.children"
        x-data="menuBuilder({ parentId: {{ $item->getKey() }} })"
        class="mt-2 space-y-2 ms-4 min-h-2"
    >
        {{-- Menu item children (actual MenuItem records) --}}
        @foreach($item->menuItemChildren as $child)
            <x-siteman::menu-item :item="$child" />
        @endforeach

        {{-- Dynamic page children (virtual, read-only) --}}
        @if($hasPageChildren)
            @foreach($item->linkable->children as $page)
                <x-siteman::page-menu-item :page="$page" />
            @endforeach
        @endif
    </ul>
</li>
