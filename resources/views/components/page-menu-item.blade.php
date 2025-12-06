@php use Siteman\Cms\Models\Page; @endphp
@props(['page', 'depth' => 0])

@php
    /** @var Page $page */
    $hasChildren = $page->children->isNotEmpty();
    $maxDepth = 3;
@endphp

<li wire:key="page-{{ $page->getKey() }}">
    <div class="flex px-3 py-2 fi-section transition-colors bg-gray-50 dark:bg-white/5">
        <div class="flex grow items-center gap-2">
            {{-- Spacer to align with menu items (no reorder handle) --}}
            <div class="w-6"></div>

            @if($hasChildren && $depth < $maxDepth)
                <x-filament::icon-button
                    icon="heroicon-o-chevron-right"
                    x-data="{ open: true }"
                    x-on:click.stop="open = !open"
                    color="gray"
                    class="transition duration-200 ease-in-out"
                    x-bind:class="{ 'rotate-90': open }"
                    size="sm"
                />
            @endif

            {{-- Page icon --}}
            <div class="flex-shrink-0 mt-0.5">
                <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>

            {{-- Two-line content --}}
            <div class="flex flex-col grow min-w-0">
                <div class="text-xs font-medium truncate text-gray-950 dark:text-white">
                    {{ $page->title }}
                </div>
                <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                    {{ $page->computed_slug }}
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-filament::badge color="gray" size="xs" class="hidden sm:block p-1 opacity-60">
                {{ __('siteman::menu.item.page_link') }}
            </x-filament::badge>
        </div>
    </div>

    @if($hasChildren && $depth < $maxDepth)
        <ul
            x-data="{ open: true }"
            x-show="open"
            x-collapse
            class="mt-2 space-y-2 ms-4"
        >
            @foreach($page->children as $child)
                <x-siteman::page-menu-item :page="$child" :depth="$depth + 1" />
            @endforeach
        </ul>
    @endif
</li>
