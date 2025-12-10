<?php declare(strict_types=1);

namespace Siteman\Cms\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Siteman\Cms\Facades\Siteman;
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
            if ((!$model->site_id) && $site = Siteman::getCurrentSite()) {
                $model->site_id = $site->id;
            }
        });

        static::addGlobalScope('site', function (Builder $query) {
            if ($site = Siteman::getCurrentSite()) {
                $table = $query->getModel()->getTable();
                $query->where("{$table}.site_id", $site->id);
            }
        });
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
