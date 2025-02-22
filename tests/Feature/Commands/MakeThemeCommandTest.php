<?php declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\Generator\ThemeGenerator;
use Siteman\Cms\Commands\MakeThemeCommand;

use function Pest\Laravel\artisan;

it('creates a new theme', function () {
    // Mock the methods used in copyStubToApp
    File::shouldReceive('exists')
        ->andReturn(false);

    File::shouldReceive('get')
        ->andReturn('stub content');

    File::shouldReceive('ensureDirectoryExists')
        ->once();

    File::shouldReceive('put')
        ->once()
        ->withArgs(function ($path, $content) {
            return str_contains($path, 'App/Themes/FancyTheme.php') && str_contains($content, 'stub content');
        });

    // Mock the File facade
    File::shouldReceive('copyDirectory')
        ->once()
        ->withArgs(function ($source, $destination) {
            return str_contains($source, 'resources/views/themes/blank') && str_contains($destination, 'resources/views/themes/fancy');
        });

    $file = mock(\Symfony\Component\Finder\SplFileInfo::class);
    $file->shouldReceive('getContents')
        ->andReturn('siteman::themes.blank test-content');
    $file->shouldReceive('getPathname')
        ->andReturn('resources/views/themes/fancy/index.blade.php');
    File::shouldReceive('allFiles')
        ->once()
        ->andReturn(collect([$file]));

    File::shouldReceive('put')
        ->once()
        ->withArgs(
            fn ($path, $content) => $path === 'resources/views/themes/fancy/index.blade.php'
            && !str_contains($content, 'siteman::themes.blank'),
        );

    // Run the command
    artisan(MakeThemeCommand::class, ['name' => 'FancyTheme'])
        ->expectsOutputToContain('Theme class created.')
        ->expectsOutputToContain('Theme views created.')
        ->expectsOutputToContain('You may add the theme in your config/siteman.php')
        ->assertExitCode(0);
});

it('it generates the theme name properly', function (string $providedName, string $themeName, string $themeNamespace) {

    $generator = mock(ThemeGenerator::class);
    $generator->shouldReceive('generate')
        ->with($themeName, $themeNamespace);
    $this->swap(ThemeGenerator::class, $generator);

    artisan(MakeThemeCommand::class, ['name' => $providedName])->assertExitCode(0);
})->with([
    ['FancyTheme', 'FancyTheme', 'App\\Themes'],
    ['Fancy', 'FancyTheme', 'App\\Themes'],
    ['Custom\\Namespace\\MyTheme', 'MyTheme', 'Custom\\Namespace'],
]);
