<?php

namespace Siteman\Cms\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Siteman\Cms\CmsServiceProvider;
use Siteman\Cms\Settings\BlogSettings;
use Spatie\Health\HealthServiceProvider;
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

    protected function getPackageProviders($app)
    {
        return [
            HealthServiceProvider::class,
            LivewireServiceProvider::class,
            CmsServiceProvider::class,
        ];
    }

    public function defineEnvironment($app): void
    {
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('siteman.models.user', User::class);

        // The settings migrations are executed after the Filament panel
        // registration. Since we check for the settings in the panel
        // registration, we need to fake the settings here.
        BlogSettings::fake([
            'enabled' => true,
            'blog_index_route' => 'blog',
            'tag_route_prefix' => 'tags',
            'rss_enabled' => true,
            'rss_endpoint' => 'rss',
        ], false);

    }

    //    protected function resolveApplicationConsoleKernel($app)
    //    {
    //        $app->singleton(
    //            Kernel::class, Kernel::class
    //        );
    //    }
}
