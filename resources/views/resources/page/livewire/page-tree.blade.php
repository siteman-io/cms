<div>
    @if($this->pages->isNotEmpty())
        <ul
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('menu', 'siteman') }}"
            x-data="menuBuilder({ parentId: 0 })"
            class="space-y-2"
        >
            @foreach($this->pages as $page)
                <x-siteman::page-tree-item :item="$page"/>
            @endforeach
        </ul>
    @endif

    <x-filament-actions::modals />
</div>
