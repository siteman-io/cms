<?php declare(strict_types=1);

namespace Siteman\Cms\Concerns;

use Illuminate\Support\Arr;
use Siteman\Cms\Enums\FormHook;
use Siteman\Cms\Facades\Siteman;

trait HasFormHooks
{
    protected static function hook(mixed $fields, FormHook $hook): array
    {
        $fields = Arr::wrap($fields);
        $hooks = Siteman::getFormHooks($hook);
        foreach ($hooks as $hook) {
            $fields = $hook($fields);
        }

        return $fields;
    }
}
