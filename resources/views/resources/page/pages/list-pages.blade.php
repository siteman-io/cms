<x-filament-panels::page>
    <div class="grid grid-cols-8 gap-6">
        <div class="col-span-2">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800">
                <h2 class="text-lg font-medium text-gray-950 dark:text-white">
{{--                    {{ __('siteman::page.tree.title') }}--}}
                </h2>
            </div>
            <div class="p-4">
                <livewire:page-tree/>
            </div>
        </div>

        <div class="col-span-6">

            @if($this->selectedPageId)
            {{-- @can('edit', $this->page) --}}
               <livewire:edit-page :record="$this->selectedPageId"/>
            {{-- @else
                <livewire:view-page :record="$this->page"/>
            @endcan --}}

            @else
                <div class="flex items-center justify-center h-full">
                    <div class="p-8 text-center max-w-md">
                        <p class="text-gray-600 dark:text-gray-400">Please select a page from the page tree to edit its content.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <x-filament-actions::modals/>
</x-filament-panels::page>
