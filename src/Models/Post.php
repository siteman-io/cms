<?php

namespace Siteman\Cms\Models;

use Siteman\Cms\Database\Factories\PostFactory;
use Illuminate\Support\Collection;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\Tags\HasTags;

/**
 * @property ?string $excerpt
 */
class Post extends BasePostType implements Feedable
{
    use HasTags;

    protected static string $factory = PostFactory::class;

    protected array $versionable = ['title', 'slug', 'excerpt', 'content', 'blocks', 'published_at'];

    public function path(): string
    {
        return "/blog/{$this->slug}";
    }

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->slug)
            ->title($this->title)
            ->summary($this->excerpt)
            ->updated($this->updated_at)
            ->link($this->path())
            ->authorName(optional($this->author)->name ?? '');
    }

    public function getFeedItems(): Collection
    {
        return self::published()->get();
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            description: $this->excerpt,
            author: property_exists($this->author, 'name') ? $this->author->name : null,
            published_time: $this->published_at,
            modified_time: $this->updated_at,
        );
    }
}
