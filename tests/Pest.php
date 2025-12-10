<?php declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Site;
use Siteman\Cms\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Browser');


function createSite(string $name = 'workbench'): Site
{
    /** @var Site $site */
    $site = Site::firstOrCreate(
        ['name' => $name],
        ['slug' => Str::slug($name), 'domain' => Str::slug($name).'.com'],
    );
    Siteman::setCurrentSite($site);

    // Set up Filament panel context for component testing
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    return $site;
}

function createUser(array $state = [], array $permissions = [], string $siteName = 'workbench'): \Workbench\App\Models\User
{
    // Create site first to ensure it exists before factory's afterCreating callbacks run
    $site = createSite($siteName);

    $user = \Workbench\App\Models\User::factory()
        ->state($state)
        ->withPermissions($permissions)
        ->create();

    $user->sites()->attach($site);

    return $user;
}
