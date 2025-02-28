<?php

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Siteman\Cms\Database\Factories\PageFactory;
use Spatie\Tags\HasTags;

class Page extends BasePostType
{
    use HasTags;

    protected static string $factory = PageFactory::class;

    public static function boot(): void
    {
        parent::boot();

        static::saving(function (self $page) {
            $prefix = $page->parent_id !== null ? $page->parent->computed_slug : '';
            $page->computed_slug = $prefix.$page->slug;
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }
}
