<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\Generator\SettingsGenerator;
use Siteman\Cms\Settings\SettingsFormInterface;
use Spatie\LaravelSettings\Settings;

it('generates a working settings class', function () {
    $generator = new SettingsGenerator;
    $className = 'TestSettings_'.uniqid();
    $namespace = 'Tests\\Generated';

    $code = $generator->generateSettings($className, $namespace, 'test');

    $tempFile = sys_get_temp_dir()."/{$className}.php";
    File::put($tempFile, $code);

    require_once $tempFile;

    $fqcn = "{$namespace}\\{$className}";

    expect(class_exists($fqcn))->toBeTrue();
    expect(is_subclass_of($fqcn, Settings::class))->toBeTrue();
    expect($fqcn::group())->toBe('test');

    File::delete($tempFile);
});

it('generates a working settings form class', function () {
    $generator = new SettingsGenerator;
    $formClassName = 'TestSettingsForm_'.uniqid();
    $settingsClassName = 'TestSettings_'.uniqid();
    $namespace = 'Tests\\Generated';

    // First generate the settings class that the form references
    $settingsCode = $generator->generateSettings($settingsClassName, $namespace, 'test');
    $settingsTempFile = sys_get_temp_dir()."/{$settingsClassName}.php";
    File::put($settingsTempFile, $settingsCode);
    require_once $settingsTempFile;

    // Generate the form class
    $formCode = $generator->generateSettingsForm($formClassName, $namespace, $settingsClassName, $namespace);
    $formTempFile = sys_get_temp_dir()."/{$formClassName}.php";
    File::put($formTempFile, $formCode);
    require_once $formTempFile;

    $fqcn = "{$namespace}\\{$formClassName}";
    $instance = new $fqcn;

    expect($instance)->toBeInstanceOf(SettingsFormInterface::class);
    expect($instance->icon())->toBe('heroicon-o-globe-alt');
    expect($instance->schema())->toBeArray();

    File::delete($settingsTempFile);
    File::delete($formTempFile);
});

it('generates a valid migration', function () {
    $generator = new SettingsGenerator;

    $code = $generator->generateMigration('test');

    // Verify it's valid PHP by evaluating it
    $migration = eval(str_replace('<?php declare(strict_types=1);', '', $code));

    expect($migration)->toBeObject();
    expect(method_exists($migration, 'up'))->toBeTrue();
});
