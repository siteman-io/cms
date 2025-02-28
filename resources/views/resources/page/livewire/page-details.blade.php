<div>
    @if ($page)
        <div class="p-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-lg font-medium text-gray-950 dark:text-white">
                {{ $page->title }}
            </h2>
        </div>

        <div class="space-y-6 p-4">
            <div>
                <h3 class="text-sm text-gray-500 dark:text-gray-400">{{ $page->slug }}</h3>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Type') }}</h3>
                    <p class="mt-1 text-sm text-gray-950 dark:text-white">{{ $page->type }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</h3>
                    <p class="mt-1 text-sm text-gray-950 dark:text-white">{{ $page->status }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Created at') }}</h3>
                    <p class="mt-1 text-sm text-gray-950 dark:text-white">{{ $page->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Updated at') }}</h3>
                    <p class="mt-1 text-sm text-gray-950 dark:text-white">{{ $page->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            @if ($page->children_count > 0)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Child Pages') }}</h3>
                    <p class="mt-1 text-sm text-gray-950 dark:text-white">{{ $page->children_count }}</p>
                </div>
            @endif

            <div class="flex space-x-4">
                <x-filament::button
                    tag="a"
                    href="{{ \Siteman\Cms\Resources\PageResource\Pages\EditPage::getUrl([$page]) }}"
                    color="primary"
                >
                    {{ __('Edit Page') }}
                </x-filament::button>

            </div>
        </div>
    @else
        <div class="flex items-center justify-center h-64">
            <p class="text-gray-500 dark:text-gray-400">{{ __('Select a page from the tree to view details') }}</p>
        </div>
    @endif
</div>
