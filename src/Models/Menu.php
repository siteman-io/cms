<?php
declare(strict_types=1);

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Siteman\Cms\Database\Factories\MenuFactory;

/**
 * @property int $id
 * @property string $name
 * @property bool $is_visible
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection|MenuLocation[] $locations
 * @property-read int|null $locations_count
 * @property-read Collection|MenuItem[] $menuItems
 * @property-read int|null $menuItems_count
 */
class Menu extends Model
{
    use HasFactory;

    protected static string $factory = MenuFactory::class;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_visible' => 'bool',
        ];
    }

    public function locations(): HasMany
    {
        return $this->hasMany(MenuLocation::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->with('menuItemChildren');
    }

    public static function location(string $location): ?self
    {
        return self::query()
            ->where('is_visible', true)
            ->whereRelation('locations', 'location', $location)
            ->with('menuItems')
            ->first();
    }
}
