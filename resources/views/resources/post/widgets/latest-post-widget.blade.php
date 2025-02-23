@php
    use Siteman\Cms\Models\Post;
    $post = Post::published()->latest()->first();
@endphp
<x-filament-widgets::widget>
    <x-filament::section>
        @if($post)
            <div class="text-gray-500 dark:text-gray-400">
                <div class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    Latest Post
                </div>
                <div class="mt-2">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $post->title }}
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $post->created_at->diffForHumans() }}
                    </div>
                </div>
                @else
                    <div class="text-gray-500 dark:text-gray-400">
                        No published post found.
                    </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
