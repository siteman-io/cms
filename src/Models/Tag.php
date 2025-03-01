<?php declare(strict_types=1);

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\Tag as SpatieTag;

class Tag extends SpatieTag
{
    public function pages(): MorphToMany
    {
        return $this->morphedByMany(Page::class, 'taggable');
    }
}
