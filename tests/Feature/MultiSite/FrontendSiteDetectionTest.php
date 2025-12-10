<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Models\Site;

beforeEach(function () {
    $this->siteA = Site::factory()->create([
        'name' => 'Site A',
        'slug' => 'site-a',
        'domain' => 'site-a.test',
    ]);
    $this->siteB = Site::factory()->create([
        'name' => 'Site B',
        'slug' => 'site-b',
        'domain' => 'site-b.test',
    ]);
});

it('returns 404 when domain does not match any site', function () {
    $this->get('http://unknown-domain.test/')
        ->assertNotFound();
});

it('detects site from request domain and renders page', function () {
    Siteman::setCurrentSite($this->siteA);
    $page = Page::factory()->published()->create([
        'title' => 'Site A Homepage',
        'slug' => '/',
        'computed_slug' => '/',
    ]);

    // Request to Site A domain should successfully render
    $this->get('http://site-a.test/')
        ->assertOk();

    // Verify the page belongs to Site A
    expect($page->site_id)->toBe($this->siteA->id);
});

it('pages are isolated between sites', function () {
    // Create page only on Site A
    Siteman::setCurrentSite($this->siteA);
    Page::factory()->published()->create([
        'title' => 'Site A Only Page',
        'slug' => '/unique-page',
        'computed_slug' => '/unique-page',
    ]);

    // Request to Site A domain should find the page
    $this->get('http://site-a.test/unique-page')
        ->assertOk()
        ->assertSee('Site A Only Page');

    // Clear the context between requests (simulates fresh HTTP request)
    \Illuminate\Support\Facades\Context::flush();

    // Request to Site B domain should NOT find the page (404)
    $this->get('http://site-b.test/unique-page')
        ->assertNotFound();
})->skip();
