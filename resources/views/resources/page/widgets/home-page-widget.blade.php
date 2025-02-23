@php
    use Siteman\Cms\Models\Page;$page = Page::where('slug', '/')->first();
@endphp
<x-filament-widgets::widget>
    <x-filament::section class="{{ $page ? 'bg-white' : 'bg-red-50' }}">
        @if($page)
            Homepage:
            <x-filament::link href="/admin/pages/{{$page->id}}/edit">{{ $page->title }}</x-filament::link>
        @else
            <x-filament::icon icon="heroicon-m-exclamation-triangle" class="inline-block h-6 w-6"/><span class="ml-6">Homepage: Not set</span>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
