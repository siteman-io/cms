<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;

class InjectAdminBar
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if (auth()->check()) {
            $content = $response->getContent();
            $content = preg_replace('/<body(\s[^>]*)?>/', '<body$1>'.Blade::render('<x-siteman::admin-bar/>'), $content);
            $response->setContent($content);
        }

        return $response;
    }
}
