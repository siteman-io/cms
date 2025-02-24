<div>
    @if($this->pages->isNotEmpty())
        <ul
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('menu', 'siteman') }}"
            x-data="menuBuilder({ parentId: 0 })"
            class="space-y-2"
        >
            @foreach($this->pages as $page)
                <livewire:page-tree-item
                    :item="$page"
                />
            @endforeach
        </ul>
    @else
        <x-filament-tables::empty-state
            icon="heroicon-o-document"
            :heading="__('siteman::page.tree.empty')"
        />
    @endif

    <x-filament-actions::modals />
</div>
