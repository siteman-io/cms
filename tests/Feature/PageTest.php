<?php

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\PageResource\Pages\CreatePage;
use Siteman\Cms\Resources\PageResource\Pages\EditPage;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('shows only published pages', function () {
    Page::factory()
        ->create([
            'published_at' => null,
            'slug' => 'draft',
        ]);

    Page::factory()
        ->published()
        ->create([
            'slug' => 'published',
        ]);

    $this->get('/draft')->assertStatus(404);
    $this->get('/published')->assertStatus(200);
});

it('needs permission to list pages', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(\Siteman\Cms\Resources\PageResource\Pages\ListPages::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_page'])->create();

    actingAs($user2)
        ->get(\Siteman\Cms\Resources\PageResource\Pages\ListPages::getUrl())
        ->assertOk();
});

it('needs permission to create pages', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(CreatePage::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_page', 'create_page'])->create();

    actingAs($user2)
        ->get(CreatePage::getUrl())
        ->assertOk();
});

it('can create pages', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'create_page'])->create());

    livewire(CreatePage::class)
        ->fillForm([
            'title' => 'Test',
            'slug' => 'test',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Page::whereSlug('test')->exists())->toBeTrue();
});

it('needs permission to update pages', function () {
    $user = User::factory()->create();

    $page = Page::factory()->create();
    actingAs($user)
        ->get(EditPage::getUrl([$page]))
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_page', 'update_page'])->create();

    actingAs($user2)
        ->get(EditPage::getUrl([$page]))
        ->assertOk();
});

it('can update pages', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page'])->create());
    $page = Page::factory()->create(['slug' => 'test']);

    livewire(EditPage::class, ['record' => $page->getRouteKey()])
        ->fillForm([
            'title' => 'Test123',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($page->refresh())->title->toBe('Test123');
});

it('needs permission to delete pages', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page'])->create());
    $page = Page::factory()->create(['slug' => 'test']);

    livewire(EditPage::class, ['record' => $page->getRouteKey()])
        ->assertActionHidden('delete');
});

it('can delete pages', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());
    $page = Page::factory()->create(['slug' => 'test']);

    livewire(EditPage::class, ['record' => $page->getRouteKey()])
        ->callAction('delete');

    expect(Page::count())->toBe(0);
});
