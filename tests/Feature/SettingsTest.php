<?php

use Siteman\Cms\Pages\SettingsPage;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

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

it('can update settings', function () {
    actingAs(User::factory()->withPermissions(['page_SettingsPage'])->create());

    $page = livewire(SettingsPage::class);
    $page->assertFormExists('generalSettingsForm');

    $page->fillForm(['site_name' => 'test'], 'generalSettingsForm')
        ->call('save', 'general')
        ->assertHasNoFormErrors(formName: 'generalSettingsForm');

    expect(app(\Siteman\Cms\Settings\GeneralSettings::class)->site_name)->toBe('test');
});
