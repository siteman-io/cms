<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;

beforeEach(function () {
    $this->siteA = createSite('site-a');
    $this->siteB = createSite('site-b');
});

it('user can access tenant they belong to', function () {
    $user = createUser();
    $secondSite = createSite('second-site');

    expect($user->sites)->toHaveCount(1);
    expect($user->canAccessTenant($secondSite))->toBeFalse();
});

it('user can belong to multiple sites', function () {
    $user = createUser();
    $secondSite = createSite('second-site');
    $user->sites()->attach($secondSite);

    expect($user->sites)->toHaveCount(2);
    expect($user->canAccessTenant($secondSite))->toBeTrue();
});

it('roles are created with site_id from current site context', function () {
    $site = createSite();
    $role = Siteman::createSuperAdminRole();

    expect($role->site_id)->toBe($site->id);
});
