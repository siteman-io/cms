<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Pages\CreatePage;

use function Pest\Livewire\livewire;

it('needs permission to create pages', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();

    $this->get(CreatePage::getUrl(tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['view_any_page', 'create_page']));

    $this->get(CreatePage::getUrl(tenant: $site))
        ->assertOk();
});

it('can create pages', function () {
    $this->actingAs(createUser(permissions: ['view_any_page', 'create_page']));

    livewire(CreatePage::class)
        ->fillForm([
            'title' => 'Test',
            'slug' => '/test',
            'type' => 'page',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Page::whereSlug('/test')->exists())->toBeTrue();
});
