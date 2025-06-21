<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages\Pages;

use Filament\Resources\Pages\ListRecords;
use Siteman\Cms\Resources\Pages\Actions\CreateAction;
use Siteman\Cms\Resources\Pages\PageResource;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
