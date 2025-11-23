<?php declare(strict_types=1);

use Livewire\Livewire;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Livewire\PageTree;
use Siteman\Cms\Resources\Pages\Pages\PageTreeSplitView;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

describe('Form-Level Permissions', function () {
    it('disables form for users without update permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page'])->create();
        actingAs($user);

        $page = Page::factory()->create([
            'title' => 'Test Page',
            'slug' => '/test',
        ]);

        $component = Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $page->id]);

        // Form should indicate it's read-only
        expect($component->instance()->isFormReadOnly())->toBeTrue();
    });

    it('enables form for users with update permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $page = Page::factory()->create([
            'title' => 'Test Page',
            'slug' => '/test',
        ]);

        $component = Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $page->id]);

        // Form should not be read-only
        expect($component->instance()->isFormReadOnly())->toBeFalse();
    });

    it('blocks save attempt by user without update permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page'])->create();
        actingAs($user);

        $page = Page::factory()->create([
            'title' => 'Original Title',
            'slug' => '/original',
        ]);

        Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $page->id])
            ->set('data.title', 'Updated Title')
            ->call('save')
            ->assertNotified();

        // Verify page was not updated
        expect($page->fresh()->title)->toBe('Original Title');
    });

    it('allows save by user with update permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $page = Page::factory()->create([
            'title' => 'Original Title',
            'slug' => '/original',
        ]);

        Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $page->id])
            ->set('data.title', 'Updated Title')
            ->call('save')
            ->assertNotified();

        // Verify page was updated
        expect($page->fresh()->title)->toBe('Updated Title');
    });
});

describe('Action-Level Permissions', function () {
    it('hides delete action in tree when user lacks delete permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page'])->create();
        actingAs($user);

        Page::factory()->create(['title' => 'Test Page', 'slug' => '/test']);

        $component = Livewire::test(PageTree::class);

        $deleteAction = $component->instance()->deleteAction();

        expect($deleteAction->isVisible())->toBeFalse();
    });

    it('shows delete action in tree when user has delete permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'delete_page'])->create();
        actingAs($user);

        Page::factory()->create(['title' => 'Test Page', 'slug' => '/test']);

        $component = Livewire::test(PageTree::class);

        $deleteAction = $component->instance()->deleteAction();

        expect($deleteAction->isVisible())->toBeTrue();
    });
});

describe('Tree Operation Permissions', function () {
    it('blocks reorder attempt by user without update permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page'])->create();
        actingAs($user);

        $page1 = Page::factory()->create(['title' => 'Page 1', 'slug' => '/page-1', 'order' => 1]);
        $page2 = Page::factory()->create(['title' => 'Page 2', 'slug' => '/page-2', 'order' => 2]);

        Livewire::test(PageTree::class)
            ->call('reorder', [$page2->id, $page1->id], null)
            ->assertNotified();

        // Verify pages were not reordered
        expect($page1->fresh()->order)->toBe(1);
        expect($page2->fresh()->order)->toBe(2);
    });

    it('allows reorder by user with update permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'update_page'])->create();
        actingAs($user);

        $page1 = Page::factory()->create(['title' => 'Page 1', 'slug' => '/page-1', 'order' => 1]);
        $page2 = Page::factory()->create(['title' => 'Page 2', 'slug' => '/page-2', 'order' => 2]);

        Livewire::test(PageTree::class)
            ->call('reorder', [$page2->id, $page1->id], null);

        // Verify pages were reordered (page2 should now be first)
        expect($page2->fresh()->order)->toBe(1);
        expect($page1->fresh()->order)->toBe(2);
    });

    it('hides reorder handle when user lacks update permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page'])->create();
        actingAs($user);

        Page::factory()->create(['title' => 'Test Page', 'slug' => '/test']);

        $component = Livewire::test(PageTree::class);

        $reorderAction = $component->instance()->reorderAction();

        expect($reorderAction->isVisible())->toBeFalse();
    });

    it('shows reorder handle when user has update permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'update_page'])->create();
        actingAs($user);

        Page::factory()->create(['title' => 'Test Page', 'slug' => '/test']);

        $component = Livewire::test(PageTree::class);

        $reorderAction = $component->instance()->reorderAction();

        expect($reorderAction->isVisible())->toBeTrue();
    });
});
