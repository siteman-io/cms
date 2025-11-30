<?php

declare(strict_types=1);

use Siteman\Cms\Commands\Generator\SettingsGenerator;

it('generates a valid settings class', function () {
    $generator = new SettingsGenerator;

    $output = $generator->generateSettings('ThemeSettings', 'App\\Settings', 'theme');

    expect($output)
        ->toContain('declare(strict_types=1);')
        ->toContain('namespace App\\Settings;')
        ->toContain('use Spatie\\LaravelSettings\\Settings;')
        ->toContain('class ThemeSettings extends Settings')
        ->toContain('public ?string $description')
        ->toContain('public static function group(): string')
        ->toContain("return 'theme';");
});

it('generates a valid settings form class', function () {
    $generator = new SettingsGenerator;

    $output = $generator->generateSettingsForm(
        'ThemeSettingsForm',
        'App\\Settings',
        'ThemeSettings',
        'App\\Settings'
    );

    expect($output)
        ->toContain('declare(strict_types=1);')
        ->toContain('namespace App\\Settings;')
        ->toContain('use Filament\\Forms\\Components\\Textarea;')
        ->toContain('use Siteman\\Cms\\Settings\\SettingsFormInterface;')
        ->toContain('class ThemeSettingsForm implements SettingsFormInterface')
        ->toContain('public static function getSettingsClass(): string')
        ->toContain('return ThemeSettings::class;')
        ->toContain('public function icon(): string')
        ->toContain("return 'heroicon-o-globe-alt';")
        ->toContain('public function schema(): array')
        ->toContain("Textarea::make('description')->rows(2)");
});

it('generates a valid migration', function () {
    $generator = new SettingsGenerator;

    $output = $generator->generateMigration('theme');

    expect($output)
        ->toContain('declare(strict_types=1);')
        ->toContain('use Spatie\\LaravelSettings\\Migrations\\SettingsMigration;')
        ->toContain('return new class extends SettingsMigration')
        ->toContain('public function up(): void')
        ->toContain("\$this->migrator->add('theme.description', 'Default value');");
});

it('generates settings with custom group name', function () {
    $generator = new SettingsGenerator;

    $output = $generator->generateSettings('SeoSettings', 'Acme\\Config', 'seo');

    expect($output)
        ->toContain('namespace Acme\\Config;')
        ->toContain('class SeoSettings extends Settings')
        ->toContain("return 'seo';");
});
