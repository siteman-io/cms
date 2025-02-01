<?php

namespace Siteman\Cms\Tests;

use Siteman\Cms\CmsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
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
    }
}
