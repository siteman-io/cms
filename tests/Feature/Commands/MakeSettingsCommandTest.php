<?php declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\MakeSettingsCommand;

use function Pest\Laravel\artisan;

it('creates a new settings class, form, and migration', function () {
    // Mock the methods used in copyStubToApp
    File::shouldReceive('exists')
        ->andReturn(false);

    File::shouldReceive('get')
        ->andReturn('stub content');
    File::shouldReceive('glob')
        ->andReturn([]);

    File::shouldReceive('ensureDirectoryExists')
        ->times(4);

    File::shouldReceive('put')
        ->times(3);

    // Run the command
    artisan(MakeSettingsCommand::class, ['name' => 'ThemeSettings'])
        ->expectsOutputToContain('Settings class created successfully.')
        ->expectsOutputToContain('Settings Form class successfully created!')
        ->expectsOutputToContain('Remember to register the SettingsForm via the configure method in your Theme')
        ->assertExitCode(0);
});
