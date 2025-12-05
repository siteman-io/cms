<?php

declare(strict_types=1);

namespace Siteman\Cms\Commands\Concerns;

use Illuminate\Support\Str;

trait ResolvesClassPath
{
    protected function getClassPath(string $namespace, string $class): string
    {
        $rootNamespace = $this->laravel->getNamespace();
        $relativePath = Str::replaceFirst($rootNamespace, '', $namespace.'\\');

        return app_path(str_replace('\\', '/', $relativePath).$class.'.php');
    }
}
