<?php declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Siteman\Cms\Blocks\BlockInterface;
use Siteman\Cms\Commands\Generator\BlockGenerator;

it('generates a working block class', function () {
    $generator = new BlockGenerator;
    $className = 'TestBlock_'.uniqid();
    $namespace = 'Tests\\Generated';

    $code = $generator->generate($className, $namespace, 'test');

    $tempFile = sys_get_temp_dir()."/{$className}.php";
    File::put($tempFile, $code);

    require_once $tempFile;

    $fqcn = "{$namespace}\\{$className}";
    $instance = new $fqcn;

    expect($instance)->toBeInstanceOf(BlockInterface::class);
    expect($instance->id())->toBe('test');

    File::delete($tempFile);
});

it('generates a valid view template', function () {
    $generator = new BlockGenerator;
    $view = $generator->generateView();

    $html = \Illuminate\Support\Facades\Blade::render($view, ['data' => ['title' => 'FooBar']]);

    expect($html)->toContain('FooBar');
});
