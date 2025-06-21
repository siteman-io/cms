<?php

namespace Siteman\Cms\Resources\Pages\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;
use Siteman\Cms\Resources\Pages\PageResource;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    #[On('page-selected')]
    public function loadPage(int $pageId): void
    {
        $this->mount($pageId);
    }
}
