<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Boost\Install\GuidelineComposer;
use Workbench\App\Models\User;
use Workbench\App\PackageGuidelineComposer;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        config()->set('siteman.models.user', User::class);
        $this->app->bind(GuidelineComposer::class, PackageGuidelineComposer::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
