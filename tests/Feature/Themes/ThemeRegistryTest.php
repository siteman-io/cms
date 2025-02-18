<?php declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Siteman\Cms\Theme\BlankTheme;
use Siteman\Cms\Theme\ThemeRegistry;

it('is empty without installed and configured themes', function () {
    $fs = Mockery::mock(Filesystem::class);
    $fs->shouldReceive('exists')->with('/vendor/path/composer/installed.json')->andReturn(false);

    $registry = new ThemeRegistry($fs, '/vendor/path');

    expect($registry->getThemes())->toBeEmpty();
});

it('returns configured themes', function () {
    $fs = Mockery::mock(Filesystem::class);
    $fs->shouldReceive('exists')->with('/vendor/path/composer/installed.json')->andReturn(false);

    $registry = new ThemeRegistry($fs, '/vendor/path', [BlankTheme::class]);

    expect($registry->getThemes())->toContain(BlankTheme::class);
});

it('merges configured and installed themes', function () {
    $fs = Mockery::mock(Filesystem::class);
    $fs->shouldReceive('exists')->with('/vendor/path/composer/installed.json')->andReturn(true);
    $fs->shouldReceive('get')->with('/vendor/path/composer/installed.json')->andReturn(json_encode([
        'packages' => [
            ['extra' => ['siteman' => ['themes' => ['Foo\\Bar\\FancyTheme']]]],
        ],
    ]));

    $registry = new ThemeRegistry($fs, '/vendor/path', [BlankTheme::class]);

    $themes = $registry->getThemes();

    expect($themes)->toContain(BlankTheme::class, 'Foo\\Bar\\FancyTheme');
});
