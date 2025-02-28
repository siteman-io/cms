<x-filament-panels::page>
    <div class="grid grid-cols-6 gap-6">
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

        <div class="col-span-4">

                <livewire:page-details :pageId="$this->selectedPageId"/>
        </div>
    </div>

    <x-filament-actions::modals/>
</x-filament-panels::page>
