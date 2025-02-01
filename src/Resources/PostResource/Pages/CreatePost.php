<?php

namespace Siteman\Cms\Resources\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Siteman\Cms\Resources\PostResource;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
