<?php

namespace Siteman\Cms\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\HasPreviewModal;
use Siteman\Cms\Resources\PageResource;

class ViewPage extends ViewRecord
{

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    #[On('page-selected')]
    public function loadPage(int $pageId): void
    {
        $this->mount($pageId);
    }
}
