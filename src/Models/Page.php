<?php

namespace Siteman\Cms\Models;

use RalphJSmit\Laravel\SEO\Support\SEOData;
use Siteman\Cms\Database\Factories\PageFactory;

class Page extends BasePostType
{
    protected static string $factory = PageFactory::class;

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            author: property_exists($this->author, 'name') ? $this->author->name : null,
            published_time: $this->published_at,
            modified_time: $this->updated_at,
        );
    }
}
