<?php

use Siteman\Cms\Pages\SettingsPage;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

it('needs permission to view settings page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(SettingsPage::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['page_SettingsPage'])->create();

    actingAs($user2)
        ->get(SettingsPage::getUrl())
        ->assertOk();
});
