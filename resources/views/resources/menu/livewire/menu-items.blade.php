<div>
    @if($this->menuItems->isNotEmpty())
        <ul
            x-load
            x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('components', 'siteman'))]"
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('menu', 'siteman') }}"
            x-data="menuBuilder({ parentId: 0 })"
            class="space-y-2"
        >
            @foreach($this->menuItems as $menuItem)
                <x-siteman::menu-item
                    :item="$menuItem"
                />
            @endforeach
        </ul>
    @endif

    <x-filament-actions::modals />
</div>
