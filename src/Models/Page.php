<?php

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Overtrue\LaravelVersionable\Versionable;
use Overtrue\LaravelVersionable\VersionStrategy;
use RalphJSmit\Laravel\SEO\Models\SEO;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Siteman\Cms\Database\Factories\PageFactory;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;

/**
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $slug
 * @property string $computed_slug
 * @property string $content
 * @property \Illuminate\Support\Collection $blocks
 * @property ?string $layout
 * @property ?Carbon $published_at
 * @property Carbon $updated_at
 * @property Carbon $created_at
 * @property User $author
 * @property SEO $seo
 */
class Page extends Model implements Feedable, HasMedia
{
    use HasFactory;
    use HasSEO;
    use HasTags;
    use InteractsWithMedia;
    use SoftDeletes;
    use Versionable;

    protected array $versionable = ['title', 'slug', 'blocks', 'content', 'published_at'];

    protected VersionStrategy $versionStrategy = VersionStrategy::SNAPSHOT;

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $post) {
            if (!$post->author_id) {
                $post->author_id = auth()->id();
            }
        });

        static::saving(function (self $page) {
            $page->slug = '/'.ltrim($page->slug, '/');
            $prefix = $page->parent_id !== null ? $page->parent->computed_slug : '';
            $page->computed_slug = $prefix.$page->slug;
        });
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('published_at', '<', now())
            ->orderBy('published_at', 'desc');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(config('siteman.models.user'), 'author_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);

        $this->addMediaConversion('featured_image')
            ->withResponsiveImages();
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'blocks' => 'collection',
            'meta' => 'collection',
        ];
    }

    public function isPublished(): bool
    {
        return $this->published_at && $this->published_at->isPast();
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            description: $this->meta['description'] ?? 'No description',
            author: property_exists($this->author, 'name') ? $this->author->name : null,
            published_time: $this->published_at,
            modified_time: $this->updated_at,
        );
    }

    protected static string $factory = PageFactory::class;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    /**
     * @return HasMany<static, $this>
     */
    public function children(): HasMany
    {
        return $this
            ->hasMany(static::class, 'parent_id')
            ->orderBy('order');
    }

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->slug)
            ->title($this->title)
            ->summary($this->excerpt ?? '')
            ->updated($this->updated_at)
            ->link($this->computed_slug)
            ->authorName($this->author->name ?? '');
    }

    public function getFeedItems(): Collection
    {
        return self::published()->where('type', 'page')->get();
    }

    public function getMeta(string $key, mixed $default = null): mixed
    {
        return $this->meta[$key] ?? $default;
    }
}
