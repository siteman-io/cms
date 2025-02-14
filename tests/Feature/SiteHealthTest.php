<?php

use Siteman\Cms\Pages\SiteHealthPage;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\ResultStore;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
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
    $user = User::factory()->create();

    actingAs($user)
        ->get(SiteHealthPage::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['page_SiteHealthPage'])->create();

    actingAs($user2)
        ->get(SiteHealthPage::getUrl())
        ->assertOk();
});

it('can execute the site health check', function () {
    actingAs(User::factory()->withPermissions(['page_SiteHealthPage'])->create());

    livewire(SiteHealthPage::class, [])->call('refresh');

    expect(app(ResultStore::class)->latestResults())->not()->toBeNull();
});

it('shows when the last check was executed', function () {
    actingAs(User::factory()->withPermissions(['page_SiteHealthPage'])->create());

    $this->get(SiteHealthPage::getUrl())
        ->assertOk()
        ->assertSeeHtml('data-testid="last-ran-at"');

    livewire(SiteHealthPage::class, [])->call('refresh');

    $this->get(SiteHealthPage::getUrl())
        ->assertOk()
        ->assertSeeHtml('data-testid="last-ran-at"');
});
