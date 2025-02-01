<?php

declare(strict_types=1);

namespace Siteman\Cms\Resources\PostResource\Pages;

use Siteman\Cms\Resources\PostResource;
use Mansoor\FilamentVersionable\RevisionsPage;

class PostRevisions extends RevisionsPage
{
    protected static string $resource = PostResource::class;
}
