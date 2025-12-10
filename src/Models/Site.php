<?php declare(strict_types=1);

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Siteman\Cms\Database\Factories\SiteFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property ?string $domain
 * @property bool $is_active
 */
class Site extends Model
{
    use HasFactory;

    protected static string $factory = SiteFactory::class;

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('siteman.models.user'),
            'site_user'
        )->withTimestamps();
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
