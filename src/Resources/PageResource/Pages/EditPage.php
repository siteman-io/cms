<?php

namespace Siteman\Cms\Resources\PageResource\Pages;

use Siteman\Cms\Resources\HasPreviewModal;
use Siteman\Cms\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;

class EditPage extends EditRecord
{
    use HasPreviewModal;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            PreviewAction::make(),
        ];
    }
}
