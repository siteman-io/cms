<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Resources\Users\Pages\ListUsers;

it('needs permission to list users', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();

    $this->get(ListUsers::getUrl(tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['view_any_user']));

    $this->get(ListUsers::getUrl(tenant: $site))
        ->assertOk();
});
