<?php
declare(strict_types=1);

namespace Siteman\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $menu_id
 * @property string $location
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Menu $menu
 */
class MenuLocation extends Model
{
    protected $guarded = [];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
