<?php

declare(strict_types=1);

namespace Siteman\Cms\Resources\PostResource\Pages;

use Mansoor\FilamentVersionable\RevisionsPage;
use Siteman\Cms\Resources\PostResource;

class PostRevisions extends RevisionsPage
{
    protected static string $resource = PostResource::class;
}
