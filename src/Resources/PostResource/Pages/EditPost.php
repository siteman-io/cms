<?php

namespace Siteman\Cms\Resources\PostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Mansoor\FilamentVersionable\Page\RevisionsAction;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use Siteman\Cms\Resources\HasPreviewModal;
use Siteman\Cms\Resources\PostResource;

class EditPost extends EditRecord
{
    use HasPreviewModal;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            RevisionsAction::make(),
            PreviewAction::make(),
        ];
    }
}
