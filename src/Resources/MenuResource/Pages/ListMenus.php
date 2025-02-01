<?php
declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Siteman\Cms\Resources\MenuResource;
use Siteman\Cms\Resources\MenuResource\HasLocationAction;

class ListMenus extends ListRecords
{
    use HasLocationAction;

    public static function getResource(): string
    {
        return MenuResource::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            $this->getLocationAction(),
        ];
    }
}
