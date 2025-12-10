<?php

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Pages\SettingsPage;

use function Pest\Livewire\livewire;

it('needs permission to view settings page', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();

    $this->get(SettingsPage::getUrl(tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['page_SettingsPage']));

    $this->get(SettingsPage::getUrl(tenant: $site))
        ->assertOk();
});

it('can update settings', function () {
    $this->actingAs(createUser(permissions: ['page_SettingsPage']));

    $page = livewire(SettingsPage::class);
    $page->assertSchemaExists('generalSettingsForm');

    $page->fillForm(['site_name' => 'test'], 'generalSettingsForm')
        ->call('save', 'general')
        ->assertHasNoFormErrors(form: 'generalSettingsForm');

    expect(app(\Siteman\Cms\Settings\GeneralSettings::class)->site_name)->toBe('test');
});
