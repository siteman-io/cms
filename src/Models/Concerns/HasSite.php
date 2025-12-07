<?php declare(strict_types=1);

namespace Siteman\Cms\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Siteman\Cms\Models\Site;

/**
 * @property int $site_id
 * @property Site $site
 */
trait HasSite
{
    public static function bootHasSite(): void
    {
        static::creating(function (Model $model) {
            if (!$model->site_id){
                $model->site_id = getPermissionsTeamId();
            }
        });
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
