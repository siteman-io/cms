<?php

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Siteman\Cms\Database\Factories\PageFactory;

class Page extends BasePostType
{
    protected static string $factory = PageFactory::class;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }
}
