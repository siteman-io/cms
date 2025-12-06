<?php

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

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
    use HasRecursiveRelationships;
    use HasSEO;
    use HasTags;
    use InteractsWithMedia;
    use SoftDeletes;
    use Versionable;

    protected array $versionable = ['title', 'slug', 'blocks', 'content', 'published_at'];

    protected VersionStrategy $versionStrategy = VersionStrategy::SNAPSHOT;

    protected $guarded = [];

    public static function getHomePage(): ?self
    {
        return Page::published()->where('computed_slug', '/')->first();
    }

    protected static function booted(): void
    {
        static::creating(function (self $page) {
            if (!$page->author_id) {
                $page->author_id = auth()->id();
            }
        });

        static::saving(function (self $page) {
            // Validate maximum nesting depth before saving
            if ($page->parent_id !== null) {
                $depth = $page->calculateDepth($page->parent_id);
                if ($depth >= 3) {
                    throw new \InvalidArgumentException('Maximum nesting depth of 3 levels exceeded.');
                }
            }

            // Validate circular reference before saving
            if ($page->parent_id !== null && $page->exists) {
                if ($page->wouldCreateCircularReference($page->parent_id)) {
                    throw new \InvalidArgumentException('Cannot set parent: this would create a circular reference.');
                }
            }

            $page->slug = '/'.ltrim($page->slug, '/');

            // Reload parent if parent_id changed to get fresh computed_slug
            if ($page->parent_id !== null) {
                $parent = $page->isDirty('parent_id')
                    ? static::find($page->parent_id)
                    : $page->parent;
                $prefix = $parent ? $parent->computed_slug : '';
            } else {
                $prefix = '';
            }

            $page->computed_slug = $prefix.$page->slug;
        });
    }

    /**
     * Calculate the depth level in the tree if this page were to have the given parent.
     *
     * Walks up the tree from the proposed parent to count the depth level.
     * Used to enforce the maximum nesting depth of 3 levels.
     *
     * @param  int|null  $parentId  The proposed parent page ID (null for root level)
     * @return int The depth level (0 for root, 1 for first level, 2 for second level, etc.)
     */
    protected function calculateDepth(?int $parentId): int
    {
        if ($parentId === null) {
            return 0; // Root level
        }

        $depth = 1; // Starting from level 1 (has a parent)
        $currentParent = static::find($parentId);

        while ($currentParent && $currentParent->parent_id !== null) {
            $depth++;
            $currentParent = $currentParent->parent;
        }

        return $depth;
    }

    /**
     * Check if setting the given parent would create a circular reference.
     *
     * Prevents a page from becoming its own ancestor by walking up the tree
     * from the proposed parent to check if this page appears in the ancestry chain.
     * Also prevents a page from being its own parent.
     *
     * @param  int|null  $parentId  The proposed parent page ID
     * @return bool True if setting this parent would create a circular reference, false otherwise
     */
    protected function wouldCreateCircularReference(?int $parentId): bool
    {
        if ($parentId === null) {
            return false;
        }

        // Cannot be parent of itself
        if ($parentId === $this->id) {
            return true;
        }

        // Check if the proposed parent is a descendant of this page
        $parent = static::find($parentId);
        if (!$parent) {
            return false;
        }

        // Walk up the tree to check if we encounter this page
        $currentParent = $parent;
        $visited = [];
        while ($currentParent && $currentParent->parent_id !== null) {
            // Prevent infinite loop
            if (in_array($currentParent->parent_id, $visited)) {
                break;
            }
            $visited[] = $currentParent->parent_id;

            if ($currentParent->parent_id === $this->id) {
                return true; // Found circular reference
            }

            $currentParent = $currentParent->parent;
        }

        return false;
    }

    /**
     * Override delete to prevent deleting pages with children.
     */
    public function delete(): ?bool
    {
        // Check if page has children (only if not in a cascade or reassign operation)
        if (!$this->skipChildrenCheck && $this->children()->count() > 0) {
            throw new \RuntimeException('Cannot delete page with children. Please reassign or cascade delete children first.');
        }

        return parent::delete();
    }

    /**
     * Flag to skip children check when deleting.
     */
    protected bool $skipChildrenCheck = false;

    /**
     * Delete this page and all its descendants (cascade delete).
     *
     * Recursively deletes all descendant pages before deleting this page.
     * Processes descendants in reverse order (bottom-up) to avoid foreign key issues.
     * Skips the children check during deletion by setting the skipChildrenCheck flag.
     */
    public function cascadeDelete(): void
    {
        // Get all descendants
        $descendants = $this->descendants()->get();

        // Delete all descendants first (bottom-up)
        foreach ($descendants->reverse() as $descendant) {
            $descendant->skipChildrenCheck = true;
            $descendant->delete();
        }

        // Delete this page
        $this->skipChildrenCheck = true;
        $this->delete();
    }

    /**
     * Delete this page and reassign children to the grandparent (or root if no grandparent).
     *
     * Moves all direct children up one level in the hierarchy before deleting this page.
     * Children will be reassigned to this page's parent (grandparent), or to root level
     * if this page is at root level. The computed_slug of reassigned children will be
     * automatically recalculated via the saving event.
     */
    public function deleteAndReassignChildren(): void
    {
        $newParentId = $this->parent_id; // Grandparent ID (or null for root)

        // Get children before updating
        $children = $this->children()->get();

        // Update all direct children to have the grandparent as parent
        foreach ($children as $child) {
            $child->parent_id = $newParentId;
            $child->save(); // This will trigger the saving event and recalculate computed_slug
        }

        // Now delete this page (no children left, so delete() will work)
        $this->skipChildrenCheck = true;
        $this->delete();
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
