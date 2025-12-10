<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Resources\Roles\Pages\CreateRole;

it('needs permission to create a role', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();

    $this->get(CreateRole::getUrl(tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['view_any_role', 'create_role']));

    $this->get(CreateRole::getUrl(tenant: $site))
        ->assertOk();
});
