<?php

namespace Siteman\Cms\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Siteman\Cms\Settings\BlogSettings;
use Siteman\Cms\Settings\GeneralSettings;
use Siteman\Cms\Theme\BlankTheme;
use Workbench\App\Models\User;

#[WithMigration]
class TestCase extends Orchestra
{
    use RefreshDatabase, WithWorkbench;

    protected $enablesPackageDiscoveries = true;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Siteman\\Cms\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    public function defineEnvironment($app): void
    {
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('siteman.models.user', User::class);

        // The settings migrations are executed after the Filament panel
        // registration. Since we check for the settings in the panel
        // registration, we need to fake the settings here.
        GeneralSettings::fake([
            'site_name' => 'Siteman',
            'description' => 'Siteman Test Environment',
            'theme' => BlankTheme::class,
        ], false);
        BlogSettings::fake([
            'enabled' => true,
            'blog_index_route' => 'blog',
            'tag_index_route' => 'tags',
            'rss_enabled' => true,
            'rss_endpoint' => 'rss',
        ], false);
    }
}
