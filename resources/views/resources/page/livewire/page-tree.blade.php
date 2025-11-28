<div wire:key="page-tree-test" class="relative">
    {{-- Loading indicator for reorder operations --}}
    <div wire:loading.delay wire:target="reorder" class="absolute inset-0 bg-white/75 dark:bg-gray-900/75 z-10 flex items-center justify-center rounded-lg">
        <div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-gray-950/5 dark:ring-white/10">
            <x-filament::loading-indicator class="h-4 w-4" />
            <span class="text-xs font-medium text-gray-950 dark:text-white">{{ __('siteman::page.tree.reordering') }}</span>
        </div>
    </div>

    @if($this->pages->isNotEmpty())
        <ul
            x-load
            x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('components', 'siteman'))]"
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('menu', 'siteman') }}"
            x-data="menuBuilder({ parentId: 0 })"
            class="space-y-2"
        >
            @foreach($this->pages as $page)
                <x-siteman::page-tree-item :item="$page"/>
            @endforeach
        </ul>
    @else
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <div class="flex justify-center mb-3">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-white/5">
                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-950 dark:text-white mb-1">
                    {{ __('siteman::page.tree.empty_tree_title') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('siteman::page.tree.empty_tree_description') }}
                </p>
            </div>
        </div>
    @endif

    <x-filament-actions::modals />
</div>
