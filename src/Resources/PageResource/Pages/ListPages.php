<?php

namespace Siteman\Cms\Resources\PageResource\Pages;

use Siteman\Cms\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PageResource\Widgets\HomePageWidget::class,
        ];
    }
}
