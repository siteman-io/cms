<form wire:submit="save">
    <x-filament::section
        :heading="__('siteman::menu.page_link')"
        :collapsible="true"
        :persist-collapsed="true"
        :collapsed="true"
        id="create-page-link"
    >
        {{ $this->form }}

        <x-slot:footerActions>
            <x-filament::button type="submit">
                {{ __('siteman::menu.resource.actions.add.label') }}
            </x-filament::button>
        </x-slot:footerActions>
    </x-filament::section>
</form>
