@php
    /**
     * @var \Siteman\Cms\Models\Page|null $page
     */
@endphp
@if($page)
<div class="hidden sm:flex items-center gap-2">
    <a
        href="{{$page->computed_slug}}"
        target="_blank"
        class="
            flex items-center h-9 px-3 text-sm font-medium
            rounded-lg shadow-sm ring-1
            ring-gray-200 bg-white text-gray-700 hover:bg-gray-50
            dark:ring-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10
            transition-colors duration-200
        "
    >
        <x-filament::icon
            icon="heroicon-o-home"
            class="w-4 h-4 me-1.5"
        />
        {{ __('siteman::dashboard.go-to-site') }}
    </a>
</div>
@endif
