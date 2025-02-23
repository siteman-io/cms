<div>
    @if($this->menuItems->isNotEmpty())
        <ul
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('menu', 'siteman') }}"
            x-data="menuBuilder({ parentId: 0 })"
            class="space-y-2"
        >
            @foreach($this->menuItems as $menuItem)
                <x-siteman::menu-item
                    :item="$menuItem"
                />
            @endforeach
        </ul>
    @else
        <x-filament-tables::empty-state
            icon="heroicon-o-document"
            :heading="__('siteman::menu.items.empty.heading')"
        />
    @endif

    <x-filament-actions::modals />
</div>
