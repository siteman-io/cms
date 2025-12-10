<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Pages\EditPage;

use function Pest\Livewire\livewire;

it('needs permission to update pages', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();
    $page = Page::factory()->create();

    $this->get(EditPage::getUrl([$page], tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['view_any_page', 'update_page']));

    $this->get(EditPage::getUrl([$page], tenant: $site))
        ->assertOk();
});

it('can update pages', function () {
    $this->actingAs(createUser(permissions: ['view_any_page', 'update_page']));
    $page = Page::factory()->create(['slug' => 'test']);

    livewire(EditPage::class, ['record' => $page->getRouteKey()])
        ->fillForm([
            'title' => 'Test123',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($page->refresh())->title->toBe('Test123');
});
