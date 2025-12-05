<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\MakeSettingsCommand;
use Siteman\Cms\Settings\SettingsFormInterface;
use Spatie\LaravelSettings\Settings;

use function Pest\Laravel\artisan;

beforeEach(function () {
    $settingsPath = app_path('Settings/TestSettings.php');
    $settingsFormPath = app_path('Settings/TestSettingsForm.php');

    if (File::exists($settingsPath)) {
        File::delete($settingsPath);
    }
    if (File::exists($settingsFormPath)) {
        File::delete($settingsFormPath);
    }
});

afterEach(function () {
    $settingsPath = app_path('Settings/TestSettings.php');
    $settingsFormPath = app_path('Settings/TestSettingsForm.php');
    $settingsDir = app_path('Settings');

    if (File::exists($settingsPath)) {
        File::delete($settingsPath);
    }
    if (File::exists($settingsFormPath)) {
        File::delete($settingsFormPath);
    }

    // Clean up any migration files created
    $migrationsPath = config('settings.migrations_paths')[0] ?? database_path('settings');
    if (File::isDirectory($migrationsPath)) {
        $files = File::glob($migrationsPath.'/*create_test_settings.php');
        foreach ($files as $file) {
            File::delete($file);
        }
    }

    if (File::isDirectory($settingsDir) && count(File::files($settingsDir)) === 0) {
        File::deleteDirectory($settingsDir);
    }
});

it('creates a new settings class, form, and migration', function () {
    artisan(MakeSettingsCommand::class, ['name' => 'TestSettings'])
        ->expectsOutputToContain('Settings class created successfully.')
        ->expectsOutputToContain('Settings Form class successfully created!')
        ->expectsOutputToContain('Remember to register the SettingsForm via the configure method in your Theme')
        ->assertExitCode(0);

    $settingsPath = app_path('Settings/TestSettings.php');
    $settingsFormPath = app_path('Settings/TestSettingsForm.php');

    expect(File::exists($settingsPath))->toBeTrue();
    expect(File::exists($settingsFormPath))->toBeTrue();

    require_once $settingsPath;
    require_once $settingsFormPath;

    expect(class_exists(\App\Settings\TestSettings::class))->toBeTrue();
    expect(is_subclass_of(\App\Settings\TestSettings::class, Settings::class))->toBeTrue();
    expect(\App\Settings\TestSettings::group())->toBe('test');

    $formInstance = new \App\Settings\TestSettingsForm;

    expect($formInstance)->toBeInstanceOf(SettingsFormInterface::class);
    expect($formInstance->icon())->toBe('heroicon-o-globe-alt');
    expect($formInstance->schema())->toBeArray();
});
