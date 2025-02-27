<?php declare(strict_types=1);

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Overtrue\LaravelVersionable\Versionable;
use Overtrue\LaravelVersionable\VersionStrategy;
use RalphJSmit\Laravel\SEO\Models\SEO;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Siteman\Cms\Facades\Siteman;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property Collection $blocks
 * @property ?string $layout
 * @property ?Carbon $published_at
 * @property Carbon $updated_at
 * @property Carbon $created_at
 * @property User $author
 * @property SEO $seo
 */
abstract class BasePostType extends Model implements HasMedia
{
    use HasFactory;
    use HasSEO;
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
            description: Siteman::getGeneralSettings()->description,
            author: property_exists($this->author, 'name') ? $this->author->name : null,
            published_time: $this->published_at,
            modified_time: $this->updated_at,
        );
    }
}
