<?php declare(strict_types=1);

use Livewire\Livewire;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Pages\EditPage;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page'])->create());
});

it('renders edit form for selected page', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    Livewire::test(EditPage::class, ['record' => $page->id])
        ->assertFormSet([
            'title' => 'Test Page',
            'slug' => '/test',
        ])
        ->assertSuccessful();
});

it('saves page updates correctly from split view', function () {
    $page = Page::factory()->create([
        'title' => 'Original Title',
        'slug' => '/original',
        'parent_id' => null,
    ]);

    Livewire::test(EditPage::class, ['record' => $page->id])
        ->fillForm([
            'title' => 'Updated Title',
            'slug' => '/updated',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $page->refresh();

    expect($page->title)->toBe('Updated Title');
    expect($page->slug)->toBe('/updated');
    expect($page->computed_slug)->toBe('/updated');
});

it('has parent selection field in edit form', function () {
    $parentPage = Page::factory()->create([
        'title' => 'Parent Page',
        'slug' => '/parent',
        'parent_id' => null,
    ]);

    $page = Page::factory()->create([
        'title' => 'Child Page',
        'slug' => '/child',
        'parent_id' => null,
    ]);

    // Verify we can fill the parent_id field (which proves it exists)
    Livewire::test(EditPage::class, ['record' => $page->id])
        ->fillForm([
            'parent_id' => $parentPage->id,
        ])
        ->assertHasNoFormErrors();
});

it('updates computed_slug when parent is changed', function () {
    $parentPage = Page::factory()->create([
        'title' => 'Parent Page',
        'slug' => '/parent',
        'parent_id' => null,
    ]);

    $page = Page::factory()->create([
        'title' => 'Child Page',
        'slug' => '/child',
        'parent_id' => null,
    ]);

    expect($page->computed_slug)->toBe('/child');

    Livewire::test(EditPage::class, ['record' => $page->id])
        ->fillForm([
            'parent_id' => $parentPage->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $page->refresh();

    expect($page->parent_id)->toBe($parentPage->id);
    expect($page->computed_slug)->toBe('/parent/child');
});

it('displays validation errors correctly in split view', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    Livewire::test(EditPage::class, ['record' => $page->id])
        ->fillForm([
            'title' => '', // Required field left empty
            'slug' => '', // Required field left empty
        ])
        ->call('save')
        ->assertHasFormErrors(['title', 'slug']);
});

it('prevents circular reference when selecting parent', function () {
    // Create parent -> child relationship
    $parentPage = Page::factory()->create([
        'title' => 'Parent Page',
        'slug' => '/parent',
        'parent_id' => null,
    ]);

    $childPage = Page::factory()->create([
        'title' => 'Child Page',
        'slug' => '/child',
        'parent_id' => $parentPage->id,
    ]);

    // Try to set child as parent of parent (circular reference)
    expect(function () use ($parentPage, $childPage) {
        Livewire::test(EditPage::class, ['record' => $parentPage->id])
            ->fillForm([
                'parent_id' => $childPage->id,
            ])
            ->call('save');
    })->toThrow(\InvalidArgumentException::class, 'Cannot set parent: this would create a circular reference.');

    // Verify parent_id was not changed
    $parentPage->refresh();
    expect($parentPage->parent_id)->toBeNull();
});

// Note: Maximum nesting depth prevention is tested at model level in
// tests/Feature/PageTree/PageRelationshipsTest.php

it('can remove parent to move page to root level', function () {
    $parentPage = Page::factory()->create([
        'title' => 'Parent Page',
        'slug' => '/parent',
        'parent_id' => null,
    ]);

    $childPage = Page::factory()->create([
        'title' => 'Child Page',
        'slug' => '/child',
        'parent_id' => $parentPage->id,
    ]);

    expect($childPage->parent_id)->toBe($parentPage->id);
    expect($childPage->computed_slug)->toBe('/parent/child');

    Livewire::test(EditPage::class, ['record' => $childPage->id])
        ->fillForm([
            'parent_id' => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $childPage->refresh();

    expect($childPage->parent_id)->toBeNull();
    expect($childPage->computed_slug)->toBe('/child');
});
