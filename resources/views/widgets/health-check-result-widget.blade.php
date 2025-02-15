<x-filament-widgets::widget>
    <x-filament::section>
        <div class="health-check-result-widget">

            <div class="flex justify-center items-center rounded-full p-2 {{ $this->getColor() }}">
                <x-filament::icon
                    icon="{{ $this->getIcon() }}"
                    class="h-8 w-8 fill-current"
                />
            </div>

            <div>
                <dd class="-mt-1 font-bold text-gray-900 dark:text-white md:mt-1 md:text-xl">
                    {{ $this->result['label'] }}
                </dd>
                <dt class="mt-0 text-sm font-medium text-gray-600 dark:text-gray-400 md:mt-1">
                    @if (!empty($this->result['notificationMessage']))
                        {{ $this->result['notificationMessage'] }}
                    @else
                        {{ $this->result['shortSummary'] }}
                    @endif
                </dt>
            </div>

        </div>

    </x-filament::section>
</x-filament-widgets::widget>
