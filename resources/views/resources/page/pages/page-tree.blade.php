<x-filament-panels::page>
    <div class="grid grid-cols-8 gap-6">
        <div class="col-span-2">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800">
                <h2 class="text-lg font-medium text-gray-950 dark:text-white">
                    {{ __('siteman::page.tree.title') }}
                </h2>
            </div>
            <div class="p-4">
                <livewire:page-tree/>
            </div>
        </div>

        <div class="col-span-6">
            @if($this->selectedPageId ?? false)
                <livewire:edit-page :record="$this->selectedPageId"/>
            @else
                <div class="flex items-center justify-center h-full">
                    <div class="p-8 text-center max-w-md">
                        <div class="text-gray-400 dark:text-gray-600 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('siteman::page.tree.empty_selection') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <x-filament-actions::modals/>
</x-filament-panels::page>
