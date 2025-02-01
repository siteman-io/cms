<?php

namespace Siteman\Cms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Siteman\Cms\Siteman
 */
class Siteman extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Siteman\Cms\Siteman::class;
    }
}
