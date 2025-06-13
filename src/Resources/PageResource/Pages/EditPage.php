<?php

namespace Siteman\Cms\Resources\PageResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Js;
use Livewire\Attributes\On;
use Siteman\Cms\Resources\HasPreviewModal;
use Siteman\Cms\Resources\PageResource;

class EditPage extends EditRecord
{
    use HasPreviewModal;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->color('gray'),
        ];
    }

    #[On('page-selected')]
    public function loadPage(int $pageId): void
    {
        $this->mount($pageId);
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->alpineClickHandler('window.location.href = '.Js::from(static::getResource()::getUrl('index')))
            ->color('gray');
    }
}
