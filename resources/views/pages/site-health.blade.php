<x-filament-panels::page>
    <div class="site-health">
        @if ($lastRanAt)
            <div
                data-testid="last-ran-at"
                class="{{ $lastRanAt->diffInMinutes() > 5 ? 'text-red-500' : 'text-gray-400 dark:text-gray-200' }} text-md text-center font-medium">
                {{ __('siteman::site-health.notifications.check_results', ['lastRanAt' => $lastRanAt->diffForHumans()]) }}
            </div>
        @endif
    </div>
</x-filament-panels::page>
