<x-filament-panels::page x-data="{ activeTab: 'general' }">
    <x-filament::tabs>
        @foreach($this->getSettingForms() as $group => $settingForm)
            <x-filament::tabs.item
                icon="{{ $settingForm->icon() }}"
                alpine-active="activeTab === '{{ $group }}'"
                x-on:click="activeTab = '{{ $group }}'">
                {{ __(sprintf('siteman::pages/settings.groups.%s.label', $group)) }}
            </x-filament::tabs.item>

        @endforeach
    </x-filament::tabs>
    @foreach($this->getSettingForms() as $group => $settingForm)
        @php
            $formProp = $group . 'SettingsForm';
        @endphp

        <template x-if="activeTab === '{{$group}}'">
            <x-filament-panels::form wire:submit="save('{{$group}}')">
                {{ $this->$formProp }}
                <div>
                    <x-filament::button type="submit" size="sm">
                        {{ __('siteman::pages/settings.form.submit') }}
                    </x-filament::button>
                </div>
            </x-filament-panels::form>
        </template>
    @endforeach
</x-filament-panels::page>
