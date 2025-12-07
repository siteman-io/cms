<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Models\Role;
use Siteman\Cms\Models\Site;

beforeEach(function () {
    $this->siteA = Site::factory()->create(['name' => 'Site A', 'slug' => 'site-a']);
    $this->siteB = Site::factory()->create(['name' => 'Site B', 'slug' => 'site-b']);
});

describe('Page isolation', function () {
    it('creates pages scoped to current site', function () {
        Siteman::setCurrentSite($this->siteA);

        $pageA = Page::factory()->create(['title' => 'Page on Site A']);

        expect($pageA->site_id)->toBe($this->siteA->id);
    });

    it('only returns pages for current site', function () {
        Siteman::setCurrentSite($this->siteA);
        Page::factory()->count(3)->create();

        Siteman::setCurrentSite($this->siteB);
        Page::factory()->count(2)->create();

        // When querying from Site A context
        Siteman::setCurrentSite($this->siteA);
        expect(Page::count())->toBe(3);

        // When querying from Site B context
        Siteman::setCurrentSite($this->siteB);
        expect(Page::count())->toBe(2);
    });

    it('does not return pages from other sites', function () {
        Siteman::setCurrentSite($this->siteA);
        $pageA = Page::factory()->create(['title' => 'Page A']);

        Siteman::setCurrentSite($this->siteB);
        $pageB = Page::factory()->create(['title' => 'Page B']);

        // From Site A context, should not see Page B
        Siteman::setCurrentSite($this->siteA);
        expect(Page::where('title', 'Page B')->exists())->toBeFalse();
        expect(Page::where('title', 'Page A')->exists())->toBeTrue();

        // From Site B context, should not see Page A
        Siteman::setCurrentSite($this->siteB);
        expect(Page::where('title', 'Page A')->exists())->toBeFalse();
        expect(Page::where('title', 'Page B')->exists())->toBeTrue();
    });
});

describe('Menu isolation', function () {
    it('creates menus scoped to current site', function () {
        Siteman::setCurrentSite($this->siteA);

        $menuA = Menu::factory()->create(['name' => 'Menu on Site A']);

        expect($menuA->site_id)->toBe($this->siteA->id);
    });

    it('only returns menus for current site', function () {
        Siteman::setCurrentSite($this->siteA);
        Menu::factory()->count(2)->create();

        Siteman::setCurrentSite($this->siteB);
        Menu::factory()->count(4)->create();

        Siteman::setCurrentSite($this->siteA);
        expect(Menu::count())->toBe(2);

        Siteman::setCurrentSite($this->siteB);
        expect(Menu::count())->toBe(4);
    });
});

describe('Role isolation', function () {
    it('creates roles scoped to current site', function () {
        Siteman::setCurrentSite($this->siteA);

        $roleA = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        expect($roleA->site_id)->toBe($this->siteA->id);
    });

    it('allows same role name on different sites', function () {
        Siteman::setCurrentSite($this->siteA);
        $roleA = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        Siteman::setCurrentSite($this->siteB);
        $roleB = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        expect($roleA->id)->not->toBe($roleB->id);
        expect($roleA->site_id)->toBe($this->siteA->id);
        expect($roleB->site_id)->toBe($this->siteB->id);
    });

    it('only returns roles for current site', function () {
        Siteman::setCurrentSite($this->siteA);
        Role::create(['name' => 'admin-a', 'guard_name' => 'web']);
        Role::create(['name' => 'editor-a', 'guard_name' => 'web']);

        Siteman::setCurrentSite($this->siteB);
        Role::create(['name' => 'admin-b', 'guard_name' => 'web']);

        Siteman::setCurrentSite($this->siteA);
        expect(Role::count())->toBe(2);
        expect(Role::where('name', 'admin-a')->exists())->toBeTrue();
        expect(Role::where('name', 'admin-b')->exists())->toBeFalse();

        Siteman::setCurrentSite($this->siteB);
        expect(Role::count())->toBe(1);
        expect(Role::where('name', 'admin-b')->exists())->toBeTrue();
    });
});
