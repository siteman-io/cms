@php use Siteman\Cms\Models\MenuItem; @endphp
@props(['item'])

@php
    /** @var MenuItem $item */

    $hasChildren = $item->children->isNotEmpty();
@endphp

<li
    wire:key="{{ $item->getKey() }}"
    data-sortable-item="{{ $item->getKey() }}"
    x-data="{ open: $persist(true).as('menu-item-' + {{ $item->getKey() }}) }"
>
    <div
        class="flex justify-between px-3 py-2 bg-white shadow-sm rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
    >
        <div class="flex items-center gap-2">
            {{ $this->reorderAction }}

            @if($hasChildren)
                <x-filament::icon-button
                    icon="heroicon-o-chevron-right"
                    x-on:click="open = !open"
                    x-bind:title="open ? '{{ trans('siteman::menu.items.collapse') }}' : '{{ trans('siteman::menu.items.expand') }}'"
                    color="gray"
                    class="transition duration-200 ease-in-out"
                    x-bind:class="{ 'rotate-90': open }"
                    size="sm"
                />
            @endif

            <div class="text-sm font-medium leading-6 text-gray-950 dark:text-white whitespace-nowrap">
                {{ \Illuminate\Support\Str::of($item->title)->limit(30) }}
            </div>

            <div class="hidden overflow-hidden text-sm text-gray-500 sm:block dark:text-gray-400 whitespace-nowrap text-ellipsis">
                {{ \Illuminate\Support\Str::of($item->url)->limit(30) }}
            </div>
        </div>
        <div class="flex items-center gap-2">
            <x-filament::badge :color="$item->type === 'internal' ? 'primary' : 'gray'" class="hidden sm:block">
                {{ $item->type }}
            </x-filament::badge>
            @php
                $editAction = ($this->editAction)(['id' => $item->getKey(), 'title' => $item->title]);
                $deleteAction = ($this->deleteAction)(['id' => $item->getKey(), 'title' => $item->title]);
            @endphp
            @if($editAction->isVisible()){{ $editAction }}@endif
            @if($deleteAction->isVisible()){{ $deleteAction }}@endif
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
            <x-siteman::menu-item :item="$child" />
        @endforeach
    </ul>
</li>
