{{-- The whole template is taken from vendor/filament/filament/resources/views/resources/pages/edit-record.blade.php --}}
{{--    <link href="{{asset('css/siteman/index.css')}}" rel="stylesheet">--}}
<x-filament-panels::page
    @class([
        'fi-resource-edit-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>

    {{--    @capture($form)--}}
{{--    <x-filament-schemas::form--}}
{{--        id="form"--}}
{{--        :wire:key="$this->getId() . '.forms.' . $this->getStatePath()"--}}
{{--        wire:submit="save"--}}
{{--    >--}}
{{--        {{ $this->form }}--}}

{{--        <x-filament-schemas::actions--}}
{{--            :actions="$this->getCachedFormActions()"--}}
{{--            :full-width="$this->hasFullWidthFormActions()"--}}
{{--        />--}}
{{--    </x-filament-schemas::form>--}}
{{--    @endcapture--}}


    {{ $this->form }}

    {{-- Customisation start --}}
    <div class="grid grid-cols-12 gap-4" wire:ignore>
        <div class="flex flex-col col-span-12 gap-4 sm:col-span-4">
            <livewire:create-page-link :menu="$record"/>

            <livewire:create-custom-link :menu="$record"/>

            <livewire:create-custom-text :menu="$record"/>
        </div>
        <div class="col-span-12 sm:col-span-8">
            <x-filament::section>
                <livewire:menu-items :menu="$record"/>
            </x-filament::section>
        </div>
    </div>
    {{-- Customisation end --}}

    <x-filament-panels::unsaved-action-changes-alert/>
</x-filament-panels::page>
