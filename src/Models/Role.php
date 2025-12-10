<?php declare(strict_types=1);

namespace Siteman\Cms\Models;

use Siteman\Cms\Models\Concerns\HasSite;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use HasSite;
}
