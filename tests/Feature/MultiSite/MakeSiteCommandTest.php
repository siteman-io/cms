<?php declare(strict_types=1);

use Siteman\Cms\Models\Site;

use function Pest\Laravel\artisan;

it('creates a site with provided arguments', function () {
    artisan('siteman:make-site', [
        'name' => 'My Test Site',
        '--slug' => 'my-test-site',
        '--domain' => 'mysite.test',
    ])->assertSuccessful();

    $site = Site::where('slug', 'my-test-site')->first();

    expect($site)->not->toBeNull();
    expect($site->name)->toBe('My Test Site');
    expect($site->slug)->toBe('my-test-site');
    expect($site->domain)->toBe('mysite.test');
});

it('auto-generates slug from name', function () {
    artisan('siteman:make-site', [
        'name' => 'My Awesome Site',
    ])->assertSuccessful();

    $site = Site::where('slug', 'my-awesome-site')->first();

    expect($site)->not->toBeNull();
    expect($site->name)->toBe('My Awesome Site');
});

it('fails when slug already exists', function () {
    Site::factory()->create(['slug' => 'existing-site']);

    artisan('siteman:make-site', [
        'name' => 'New Site',
        '--slug' => 'existing-site',
    ])->assertFailed();
});

it('fails when domain already exists', function () {
    Site::factory()->create(['domain' => 'taken.test']);

    artisan('siteman:make-site', [
        'name' => 'New Site',
        '--domain' => 'taken.test',
    ])->assertFailed();
});

it('creates site without domain when not provided', function () {
    artisan('siteman:make-site', [
        'name' => 'Site Without Domain',
    ])->assertSuccessful();

    $site = Site::where('slug', 'site-without-domain')->first();

    expect($site)->not->toBeNull();
    expect($site->domain)->toBeNull();
});
