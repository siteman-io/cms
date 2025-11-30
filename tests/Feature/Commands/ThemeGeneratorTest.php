<?php

declare(strict_types=1);

use Siteman\Cms\Commands\Generator\ThemeGenerator;

it('generates a valid theme class', function () {
    $generator = new ThemeGenerator;

    $output = $generator->generate('FancyTheme', 'App\\Themes');

    expect($output)
        ->toContain('declare(strict_types=1);')
        ->toContain('namespace App\\Themes;')
        ->toContain('use Siteman\\Cms\\Siteman;')
        ->toContain('use Siteman\\Cms\\Theme\\ThemeInterface;')
        ->toContain('use Siteman\\Cms\\Theme\\BaseLayout;')
        ->toContain('class FancyTheme implements ThemeInterface')
        ->toContain('public static function getName(): string')
        ->toContain("return 'FancyTheme';")
        ->toContain('public function configure(Siteman $siteman): void')
        ->toContain("->registerMenuLocation('header', 'Header')")
        ->toContain("->registerMenuLocation('footer', 'Footer')")
        ->toContain('$siteman->registerLayout(BaseLayout::class);');
});

it('generates theme with custom namespace', function () {
    $generator = new ThemeGenerator;

    $output = $generator->generate('DarkTheme', 'Acme\\Cms\\Themes');

    expect($output)
        ->toContain('namespace Acme\\Cms\\Themes;')
        ->toContain('class DarkTheme implements ThemeInterface')
        ->toContain("return 'DarkTheme';");
});

it('calculates correct class file path', function () {
    $generator = new ThemeGenerator;

    $path = $generator->getClassFilePath('App\\Themes', 'FancyTheme');

    expect($path)->toEndWith('App/Themes/FancyTheme.php');
});

it('calculates correct class file path for nested namespace', function () {
    $generator = new ThemeGenerator;

    $path = $generator->getClassFilePath('Acme\\Cms\\Themes', 'DarkTheme');

    expect($path)->toEndWith('Acme/Cms/Themes/DarkTheme.php');
});
