<?php

namespace Siteman\Cms\Resources\Pages\Pages;

use Filament\Resources\Pages\CreateRecord;
use Siteman\Cms\Resources\Pages\PageResource;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}
