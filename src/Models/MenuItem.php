<?php

declare(strict_types=1);

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Siteman\Cms\Database\Factories\MenuItemFactory;

/**
 * @property int $id
 * @property int $menu_id
 * @property int|null $parent_id
 * @property string $title
 * @property string|null $url
 * @property string|null $type
 * @property string|null $target
 * @property int $order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection|MenuItem[] $children
 * @property-read int|null $children_count
 * @property-read Model|null $linkable
 * @property-read Menu $menu
 * @property-read MenuItem|null $parent
 */
class MenuItem extends Model
{
    use HasFactory;

    protected static string $factory = MenuItemFactory::class;

    protected $guarded = [];

    protected $with = ['linkable'];

    protected static function booted(): void
    {
        static::deleted(function (self $menuItem) {
            $menuItem->children->each->delete();
        });
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')
            ->with('children')
            ->orderBy('order');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function url(): Attribute
    {
        return Attribute::get(
            fn (?string $value) => $this->linkable instanceof Page
                ? '/'.ltrim($this->linkable->slug, '/')
                : $value,
        );
    }

    protected function type(): Attribute
    {
        return Attribute::get(function () {
            if ($this->linkable instanceof Page) {
                return __('siteman::menu.item.page_link');
            }
            if (is_null($this->linkable) && is_null($this->url)) {
                return __('siteman::menu.item.custom_text');
            }

            return __('siteman::menu.item.custom_link');
        });
    }
}
