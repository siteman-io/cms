<?php declare(strict_types=1);

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Livewire\PageTree;
use Siteman\Cms\Resources\Pages\Pages\PageTreeSplitView;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Create Child Action', function () {
    it('shows create child action for users with create permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'create_page'])->create();
        actingAs($user);

        $page = Page::factory()->create(['title' => 'Parent Page', 'slug' => '/parent']);

        Livewire::test(PageTree::class)
            ->assertActionVisible(TestAction::make('createChild')->arguments(['id' => $page->id]));
    });

    it('hides create child action for users without create permission', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page'])->create();
        actingAs($user);

        $page = Page::factory()->create(['title' => 'Parent Page', 'slug' => '/parent']);

        Livewire::test(PageTree::class)
            ->assertActionHidden(TestAction::make('createChild')->arguments(['id' => $page->id]));
    });

    it('disables create child action at max depth (level 3)', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'create_page'])->create();
        actingAs($user);

        // Create a page at level 3 (depth = 2)
        $root = Page::factory()->create(['title' => 'Root', 'slug' => '/root', 'parent_id' => null]);
        $level2 = Page::factory()->create(['title' => 'Level 2', 'slug' => '/level-2', 'parent_id' => $root->id]);
        $level3 = Page::factory()->create(['title' => 'Level 3', 'slug' => '/level-3', 'parent_id' => $level2->id]);

        Livewire::test(PageTree::class)
            ->assertActionDisabled(TestAction::make('createChild')->arguments(['id' => $level3->id]));
    });

    it('enables create child action below max depth', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'create_page'])->create();
        actingAs($user);

        // Create a page at level 2 (depth = 1)
        $root = Page::factory()->create(['title' => 'Root', 'slug' => '/root', 'parent_id' => null]);
        $level2 = Page::factory()->create(['title' => 'Level 2', 'slug' => '/level-2', 'parent_id' => $root->id]);

        Livewire::test(PageTree::class)
            ->assertActionEnabled(TestAction::make('createChild')->arguments(['id' => $level2->id]));
    });

    it('creates child page with modal form', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'create_page'])->create();
        actingAs($user);

        $parent = Page::factory()->create(['title' => 'Parent Page', 'slug' => '/parent']);

        Livewire::test(PageTree::class)
            ->callAction(
                TestAction::make('createChild')->arguments(['id' => $parent->id]),
                data: ['title' => 'New Child Page', 'type' => 'page']
            )
            ->assertHasNoFormErrors()
            ->assertNotified();

        // Verify the child page was created using database assertion
        $this->assertDatabaseHas('pages', [
            'title' => 'New Child Page',
            'slug' => '/new-child-page',
            'type' => 'page',
            'parent_id' => $parent->id,
            'author_id' => $user->id,
        ]);
    });
});

describe('Parent Navigation', function () {
    it('shows go to parent link when page has parent', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $parent = Page::factory()
            ->withChildren(1)
            ->create(['title' => 'Parent Page']);
        $child = $parent->children->first();

        // Test via Livewire component - the parent relationship is loaded when selectedPage is set
        Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $child->id])
            ->assertSet('selectedPage.parent_id', $parent->id)
            ->assertSee('Go to Parent')
            ->assertSee($parent->title);
    });

    it('hides go to parent link when page has no parent', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $root = Page::factory()->create(['title' => 'Root Page', 'slug' => '/root', 'parent_id' => null]);

        $response = get(\Siteman\Cms\Resources\Pages\PageResource::getUrl('index', ['selectedPageId' => $root->id]));

        $response->assertOk()
            ->assertDontSee('Go to Parent');
    });
});

describe('Breadcrumb Navigation', function () {
    it('shows clickable breadcrumbs for nested page', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $root = Page::factory()->create(['title' => 'Root Page', 'slug' => '/root']);
        $level2 = Page::factory()->create(['title' => 'Level 2', 'slug' => '/level-2', 'parent_id' => $root->id]);
        $level3 = Page::factory()->create(['title' => 'Level 3', 'slug' => '/level-3', 'parent_id' => $level2->id]);

        $component = Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $level3->id]);

        $breadcrumbs = $component->instance()->getBreadcrumbs();

        // Should have 4 items: Tree page + Root + Level2 + Level3
        expect($breadcrumbs)->toHaveCount(4)
            ->and($breadcrumbs)->toContain('Page Tree')
            ->and($breadcrumbs)->toContain('Root Page')
            ->and($breadcrumbs)->toContain('Level 2')
            ->and($breadcrumbs)->toContain('Level 3');

        // Check that all items have URLs as keys
        $urls = array_keys($breadcrumbs);
        expect($urls)->each->toBeString()
            ->and(count($urls))->toBe(4);
    });

    it('breadcrumb URLs are clickable and navigate to pages', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $root = Page::factory()->create(['title' => 'Root Page', 'slug' => '/root']);
        $child = Page::factory()->create(['title' => 'Child Page', 'slug' => '/child', 'parent_id' => $root->id]);

        Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $child->id])
            ->dispatch('page-selected', pageId: $root->id)
            ->assertSet('selectedPageId', $root->id);
    });

    it('hides breadcrumbs when no page is selected', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $component = Livewire::test(PageTreeSplitView::class);
        $breadcrumbs = $component->instance()->getBreadcrumbs();

        // Should only have the tree page itself
        expect($breadcrumbs)->toHaveCount(1);
    });
});

describe('Children Navigation', function () {
    it('shows children links when page has children', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $parent = Page::factory()
            ->withChildren([
                ['title' => 'Child 1'],
                ['title' => 'Child 2'],
            ])
            ->create(['title' => 'Parent Page']);

        // Test via Livewire component - children relationship is loaded when selectedPage is set
        Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $parent->id])
            ->assertSee('Children')
            ->assertSee('Child 1')
            ->assertSee('Child 2');
    });

    it('hides children section when page has no children', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $leaf = Page::factory()->create(['title' => 'Leaf Page', 'slug' => '/leaf']);

        $response = get(\Siteman\Cms\Resources\Pages\PageResource::getUrl('index', ['selectedPageId' => $leaf->id]));

        $response->assertOk()
            ->assertDontSee('Children (');
    });

    it('navigates to child page when clicked', function () {
        $user = User::factory()->withPermissions(['view_any_page', 'view_page', 'update_page'])->create();
        actingAs($user);

        $parent = Page::factory()->create(['title' => 'Parent Page', 'slug' => '/parent']);
        $child = Page::factory()->create(['title' => 'Child Page', 'slug' => '/child', 'parent_id' => $parent->id]);

        Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $parent->id])
            ->dispatch('page-selected', pageId: $child->id)
            ->assertSet('selectedPageId', $child->id);
    });
});
