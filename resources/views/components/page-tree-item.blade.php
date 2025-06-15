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
        class="flex justify-between px-3 py-2 bg-white shadow-sm rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
    >
        <div class="flex items-center gap-2">
            {{ $this->reorderAction }}
            @if($item->children_count > 0)
                <x-filament::icon-button
                    icon="heroicon-o-chevron-right"
                    x-on:click.stop="open = !open; if (open) { $wire.loadChildren({{ $item->getKey() }}) }"
                    x-bind:title="open ? '{{ __('siteman::page.tree.items.collapse') }}' : '{{ __('siteman::page.tree.items.expand') }}'"
                    color="gray"
                    class="transition duration-200 ease-in-out"
                    x-bind:class="{ 'rotate-90': open }"
                    size="sm"
                />
            @endif

            <button
                wire:click="selectPage({{ $item->getKey() }})"
                class="flex items-center gap-2 text-left hover:text-primary-100 focus:outline-hidden focus:text-primary-500"
            >
                <div
                    class="hidden overflow-hidden text-sm text-gray-500 sm:block dark:text-gray-400 whitespace-nowrap text-ellipsis">
                    {{ \Illuminate\Support\Str::of($item->slug)->limit(15) }}
                </div>
            </button>
        </div>
        <div class="flex items-center gap-2">
            <x-filament::badge :color="$item->type === 'internal' ? 'primary' : 'gray'" class="hidden sm:block">
                {{ $item->type }}
            </x-filament::badge>
            @php
                //                $editAction = ($this->editAction)(['id' => $item->getKey()]);
                                $deleteAction = ($this->deleteAction)(['id' => $item->getKey()]);
            @endphp
            <x-filament-actions::group :actions="[
//        $editAction->isVisible() ? $editAction : null,
        $deleteAction->isVisible() ? $deleteAction : null,
    ]"/>
        </div>
    </div>

    <ul
        x-collapse
        x-show="open"
        wire:key="{{ $item->getKey() }}.children"
        x-data="menuBuilder({ parentId: {{ $item->getKey()  }} })"
        class="mt-2 space-y-2 ms-4"
    >
        @if($item->relationLoaded('children'))
            @foreach($item->children as $child)
                <x-siteman::page-tree-item :item="$child"/>
            @endforeach
        @endif
    </ul>
</li>
