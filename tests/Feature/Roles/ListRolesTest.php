<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Resources\Roles\Pages\ListRoles;

it('needs permission to list roles', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();

    $this->get(ListRoles::getUrl(tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['view_any_role']));

    $this->get(ListRoles::getUrl(tenant: $site))
        ->assertOk();
});
