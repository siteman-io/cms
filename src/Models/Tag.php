<?php declare(strict_types=1);

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Siteman\Cms\Settings\BlogSettings;
use Spatie\Tags\Tag as SpatieTag;

class Tag extends SpatieTag
{
    public function pages(): MorphToMany
    {
        return $this->morphedByMany(Page::class, 'taggable');
    }

    public function url(): string
    {
        return '/'.app(BlogSettings::class)->tag_index_route.'/'.$this->slug;
    }
}
