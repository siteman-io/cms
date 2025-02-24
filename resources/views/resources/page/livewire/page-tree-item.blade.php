@php use Siteman\Cms\Models\Page; @endphp
@props(['item'])

@php
    /** @var Page $item */
@endphp

<li
    wire:key="{{ $item->getKey() }}"
    data-sortable-item="{{ $item->getKey() }}"
    x-data="{ open: false }"
>
    <div
        class="flex justify-between px-3 py-2 bg-white shadow-sm rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
    >
        <div class="flex items-center gap-2">
            <x-filament::icon-button
                icon="heroicon-o-chevron-right"
                x-on:click="open = !open; if (open) { $wire.loadChildren() } else { $wire.resetChildrenLoaded() }"
                x-bind:title="open ? '{{ __('siteman::page.tree.items.collapse') }}' : '{{ __('siteman::page.tree.items.expand') }}'"
                color="gray"
                class="transition duration-200 ease-in-out"
                x-bind:class="{ 'rotate-90': open }"
                size="sm"
            />

            <div class="text-sm font-medium leading-6 text-gray-950 dark:text-white whitespace-nowrap">
                {{ \Illuminate\Support\Str::of($item->title)->limit(30) }}
            </div>

            <div
                class="hidden overflow-hidden text-sm text-gray-500 sm:block dark:text-gray-400 whitespace-nowrap text-ellipsis">
                {{ \Illuminate\Support\Str::of($item->slug)->limit(30) }}
            </div>
        </div>
        <div class="flex items-center gap-2">
            <x-filament::badge :color="$item->type === 'internal' ? 'primary' : 'gray'" class="hidden sm:block">
                {{ $item->type }}
            </x-filament::badge>
            <x-filament-actions::group :actions="[
        $this->deleteAction,
    ]" />
        </div>
    </div>

    <ul
        x-collapse
        x-show="open"
        wire:key="{{ $item->getKey() }}.children"
        class="mt-2 space-y-2 ms-4"
    >
        @if($childrenLoaded && $children?->isNotEmpty())
            @foreach($children as $child)
                <livewire:page-tree-item :item="$child"/>
            @endforeach
        @endif
    </ul>
</li>
