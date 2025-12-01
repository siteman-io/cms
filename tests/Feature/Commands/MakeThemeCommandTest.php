<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\MakeThemeCommand;
use Siteman\Cms\Theme\ThemeInterface;

use function Pest\Laravel\artisan;

beforeEach(function () {
    $themePath = app_path('Themes/TestTheme.php');
    $viewsPath = resource_path('views/themes/test');

    if (File::exists($themePath)) {
        File::delete($themePath);
    }
    if (File::isDirectory($viewsPath)) {
        File::deleteDirectory($viewsPath);
    }
});

afterEach(function () {
    $themePath = app_path('Themes/TestTheme.php');
    $viewsPath = resource_path('views/themes/test');
    $themesDir = app_path('Themes');

    if (File::exists($themePath)) {
        File::delete($themePath);
    }
    if (File::isDirectory($viewsPath)) {
        File::deleteDirectory($viewsPath);
    }
    if (File::isDirectory($themesDir) && count(File::files($themesDir)) === 0) {
        File::deleteDirectory($themesDir);
    }
});

it('creates a new theme', function () {
    artisan(MakeThemeCommand::class, ['name' => 'TestTheme'])
        ->expectsOutputToContain('Theme class created.')
        ->expectsOutputToContain('Theme views created.')
        ->expectsOutputToContain('You may add the theme in your config/siteman.php')
        ->assertExitCode(0);

    $themePath = app_path('Themes/TestTheme.php');

    expect(File::exists($themePath))->toBeTrue();

    require_once $themePath;

    $instance = new \App\Themes\TestTheme;

    expect($instance)->toBeInstanceOf(ThemeInterface::class);
    expect(\App\Themes\TestTheme::getName())->toBe('TestTheme');
});

it('generates the theme name properly with suffix', function () {
    artisan(MakeThemeCommand::class, ['name' => 'Fancy'])
        ->assertExitCode(0);

    $themePath = app_path('Themes/FancyTheme.php');
    expect(File::exists($themePath))->toBeTrue();

    require_once $themePath;

    $instance = new \App\Themes\FancyTheme;

    expect($instance)->toBeInstanceOf(ThemeInterface::class);
    expect(\App\Themes\FancyTheme::getName())->toBe('FancyTheme');

    // Cleanup
    File::delete($themePath);
});

it('generates the theme name properly when already has suffix', function () {
    artisan(MakeThemeCommand::class, ['name' => 'FancyTheme'])
        ->assertExitCode(0);

    $themePath = app_path('Themes/FancyTheme.php');
    expect(File::exists($themePath))->toBeTrue();

    require_once $themePath;

    $instance = new \App\Themes\FancyTheme;

    expect($instance)->toBeInstanceOf(ThemeInterface::class);
    expect(\App\Themes\FancyTheme::getName())->toBe('FancyTheme');

    // Cleanup
    File::delete($themePath);
});
