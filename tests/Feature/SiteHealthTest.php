<?php

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Pages\SiteHealthPage;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;

use function Pest\Livewire\livewire;

it('registers the checks properly', function () {
    expect(Health::registeredChecks()->whereInstanceOf(EnvironmentCheck::class))
        ->toHaveCount(1)
        ->and(Health::registeredChecks()->whereInstanceOf(OptimizedAppCheck::class))
        ->toHaveCount(1)
        ->and(Health::registeredChecks()->whereInstanceOf(ScheduleCheck::class))
        ->toHaveCount(1)
        ->and(Health::registeredChecks()->whereInstanceOf(CacheCheck::class))
        ->toHaveCount(1);
});

it('runs registers the scheduler correctly', function () {
    expect($events = \Illuminate\Support\Facades\Schedule::events())
        ->not()
        ->toBeEmpty()
        ->and($events[0]->command)
        ->toContain('health:check');
});

it('needs permission to view site health page', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();

    $this->get(SiteHealthPage::getUrl(tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['page_SiteHealthPage']));

    $this->get(SiteHealthPage::getUrl(tenant: $site))
        ->assertOk();
});

it('can execute the site health check', function () {
    $this->actingAs(createUser(permissions: ['page_SiteHealthPage']));

    livewire(SiteHealthPage::class, [])->call('refresh');

    expect(app(ResultStore::class)->latestResults())->not()->toBeNull();
});

it('shows when the last check was executed', function () {
    $this->actingAs(createUser(permissions: ['page_SiteHealthPage']));

    Artisan::call(RunHealthChecksCommand::class);

    $component = livewire(SiteHealthPage::class, [])->assertOk();

    app(ResultStore::class)
        ->latestResults()
        ->storedCheckResults
        ->each(function (StoredCheckResult $result) use ($component) {
            $component->assertSee($result->label);
        });
});
