<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\MakeSettingsCommand;

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

    $settingsContent = File::get($settingsPath);
    expect($settingsContent)
        ->toContain('class TestSettings extends Settings')
        ->toContain("return 'test';")
        ->toContain('public ?string $description');

    $formContent = File::get($settingsFormPath);
    expect($formContent)
        ->toContain('class TestSettingsForm implements SettingsFormInterface')
        ->toContain('return TestSettings::class;')
        ->toContain("Textarea::make('description')->rows(2)");
});
