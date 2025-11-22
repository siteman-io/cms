<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can have parent-child relationships', function () {
    $parent = Page::factory()->create([
        'title' => 'Parent Page',
        'slug' => '/parent',
        'parent_id' => null,
    ]);

    $child = Page::factory()->create([
        'title' => 'Child Page',
        'slug' => '/child',
        'parent_id' => $parent->id,
    ]);

    // Refresh to load relationships
    $parent->refresh();
    $child->refresh();

    // Assert parent has children
    expect($parent->children)->toHaveCount(1);
    expect($parent->children->first()->id)->toBe($child->id);

    // Assert child has parent
    expect($child->parent)->not->toBeNull();
    expect($child->parent->id)->toBe($parent->id);

    // Assert computed_slug is correct
    expect($child->computed_slug)->toBe('/parent/child');
});

it('automatically generates computed_slug from hierarchy', function () {
    $grandparent = Page::factory()->create([
        'title' => 'Grandparent',
        'slug' => '/grandparent',
        'parent_id' => null,
    ]);

    $parent = Page::factory()->create([
        'title' => 'Parent',
        'slug' => '/parent',
        'parent_id' => $grandparent->id,
    ]);

    $child = Page::factory()->create([
        'title' => 'Child',
        'slug' => '/child',
        'parent_id' => $parent->id,
    ]);

    // Assert slugs are computed correctly at each level
    expect($grandparent->computed_slug)->toBe('/grandparent');
    expect($parent->computed_slug)->toBe('/grandparent/parent');
    expect($child->computed_slug)->toBe('/grandparent/parent/child');
});

it('enforces maximum nesting depth of 3 levels', function () {
    // Level 1 (root)
    $level1 = Page::factory()->create([
        'title' => 'Level 1',
        'slug' => '/level-1',
        'parent_id' => null,
    ]);

    // Level 2
    $level2 = Page::factory()->create([
        'title' => 'Level 2',
        'slug' => '/level-2',
        'parent_id' => $level1->id,
    ]);

    // Level 3 (deepest allowed)
    $level3 = Page::factory()->create([
        'title' => 'Level 3',
        'slug' => '/level-3',
        'parent_id' => $level2->id,
    ]);

    // This should succeed - we're at max depth
    expect($level3->exists)->toBeTrue();
    expect($level3->parent_id)->toBe($level2->id);

    // Level 4 should be rejected (too deep)
    expect(function () use ($level3) {
        Page::factory()->create([
            'title' => 'Level 4',
            'slug' => '/level-4',
            'parent_id' => $level3->id,
        ]);
    })->toThrow(\InvalidArgumentException::class, 'Maximum nesting depth of 3 levels exceeded');
});

it('updates computed_slug when moving to a new parent', function () {
    $parent1 = Page::factory()->create([
        'title' => 'Parent 1',
        'slug' => '/parent-1',
        'parent_id' => null,
    ]);

    $parent2 = Page::factory()->create([
        'title' => 'Parent 2',
        'slug' => '/parent-2',
        'parent_id' => null,
    ]);

    $child = Page::factory()->create([
        'title' => 'Child',
        'slug' => '/child',
        'parent_id' => $parent1->id,
    ]);

    // Initial computed_slug
    expect($child->computed_slug)->toBe('/parent-1/child');

    // Move to different parent
    $child->parent_id = $parent2->id;
    $child->save();

    // Computed_slug should update
    expect($child->refresh()->computed_slug)->toBe('/parent-2/child');

    // Move to root level
    $child->parent_id = null;
    $child->save();

    // Computed_slug should be just the slug
    expect($child->refresh()->computed_slug)->toBe('/child');
});
