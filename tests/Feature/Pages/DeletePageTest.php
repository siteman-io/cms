<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Pages\EditPage;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

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
