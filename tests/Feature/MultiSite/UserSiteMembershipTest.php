<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Site;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->siteA = Site::factory()->create(['name' => 'Site A', 'slug' => 'site-a']);
    $this->siteB = Site::factory()->create(['name' => 'Site B', 'slug' => 'site-b']);
});

it('user can belong to multiple sites', function () {
    $user = User::factory()->create();
    $user->sites()->attach([$this->siteA->id, $this->siteB->id]);

    expect($user->sites)->toHaveCount(2);
    expect($user->sites->pluck('id')->toArray())->toContain($this->siteA->id, $this->siteB->id);
});

it('user can access tenant they belong to', function () {
    $user = User::factory()->forSite($this->siteA)->create();

    expect($user->canAccessTenant($this->siteA))->toBeTrue();
    expect($user->canAccessTenant($this->siteB))->toBeFalse();
});

it('user can access multiple tenants they belong to', function () {
    $user = User::factory()->create();
    $user->sites()->attach([$this->siteA->id, $this->siteB->id]);

    expect($user->canAccessTenant($this->siteA))->toBeTrue();
    expect($user->canAccessTenant($this->siteB))->toBeTrue();
});

it('user sites relationship returns all sites', function () {
    $user = User::factory()->create();
    $user->sites()->attach([$this->siteA->id, $this->siteB->id]);

    expect($user->sites)->toHaveCount(2);
    expect($user->sites->pluck('id'))->toContain($this->siteA->id);
    expect($user->sites->pluck('id'))->toContain($this->siteB->id);
});

it('user cannot access site they do not belong to', function () {
    $user = User::factory()->forSite($this->siteA)->create();

    expect($user->canAccessTenant($this->siteB))->toBeFalse();
});

it('roles are created with site_id from current site context', function () {
    Siteman::setCurrentSite($this->siteA);
    $role = Siteman::createSuperAdminRole();

    expect($role->site_id)->toBe($this->siteA->id);
});
