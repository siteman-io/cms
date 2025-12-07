<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Middleware;

use Filament\Facades\Filament;
use Illuminate\Http\Request;

class SitePermissionMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if (!empty(auth()->user())) {
            setPermissionsTeamId(Filament::getTenant()->getKey());
        }

        return $next($request);
    }
}
