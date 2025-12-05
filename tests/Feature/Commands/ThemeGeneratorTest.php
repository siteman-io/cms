<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\Generator\ThemeGenerator;
use Siteman\Cms\Theme\ThemeInterface;

it('generates a working theme class', function () {
    $generator = new ThemeGenerator;
    $className = 'TestTheme_'.uniqid();
    $namespace = 'Tests\\Generated';

    $code = $generator->generate($className, $namespace);

    $tempFile = sys_get_temp_dir()."/{$className}.php";
    File::put($tempFile, $code);

    require_once $tempFile;

    $fqcn = "{$namespace}\\{$className}";
    $instance = new $fqcn;

    expect($instance)->toBeInstanceOf(ThemeInterface::class);
    expect($fqcn::getName())->toBe($className);

    File::delete($tempFile);
});
