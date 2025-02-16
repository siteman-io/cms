<?php

namespace Siteman\Cms\Models;

use Siteman\Cms\Database\Factories\PageFactory;

class Page extends BasePostType
{
    protected static string $factory = PageFactory::class;
}
