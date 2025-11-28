---
outline: deep
---

# Testing Guide

Siteman CMS uses Pest PHP for testing, providing a modern and expressive testing experience. This guide covers how to write and run tests for custom blocks, themes, and features.

## Test Stack

Siteman's testing stack includes:

- **[Pest PHP](https://pestphp.com/)**: Modern PHP testing framework
- **[Pest Laravel Plugin](https://pestphp.com/docs/plugins#laravel)**: Laravel-specific helpers
- **[Pest Livewire Plugin](https://pestphp.com/docs/plugins#livewire)**: Testing Filament components
- **[Pest Browser Plugin](https://pestphp.com/docs/plugins#browser)**: End-to-end browser testing
- **[Orchestra Testbench](https://packages.tools/testbench)**: Package testing environment

## Running Tests

### Run All Tests

```bash
composer test
```

This runs:
1. Laravel Pint (code formatting check)
2. PHPStan (static analysis)
3. Pest (test suite)

### Run Only Pest Tests

```bash
vendor/bin/pest
```

### Run Specific Test File

```bash
vendor/bin/pest tests/Feature/PageTest.php
```

### Run Tests with Filter

```bash
vendor/bin/pest --filter="can create page"
```

### Run Tests in Parallel

```bash
vendor/bin/pest --parallel
```

### Generate Coverage Report

```bash
composer test-coverage
```

Generates HTML coverage report in `coverage/` directory.

## Writing Tests

### Test Structure

Tests are organized in:

```
tests/
├── Feature/           # Integration/feature tests
│   ├── BlogTest.php
│   ├── MenuTest.php
│   └── PageTest.php
├── Unit/              # Unit tests
│   ├── BlockTest.php
│   └── ThemeTest.php
├── Pest.php           # Pest configuration
└── TestCase.php       # Base test case
```

### Basic Test Example

```php
<?php

use Siteman\Cms\Models\Page;
use function Pest\Laravel\{get, assertDatabaseHas};

it('displays a published page', function () {
    $page = Page::factory()->create([
        'title' => 'About Us',
        'computed_slug' => '/about',
        'published_at' => now()->subDay(),
    ]);

    get('/about')
        ->assertOk()
        ->assertSee('About Us');
});
```

### Using Factories

Siteman provides factories for testing:

```php
use Siteman\Cms\Models\Page;
use App\Models\User;

// Create a published page
$page = Page::factory()->published()->create();

// Create a draft page
$page = Page::factory()->create([
    'published_at' => null,
]);

// Create page with author
$author = User::factory()->create();
$page = Page::factory()->create([
    'author_id' => $author->id,
]);

// Create page with tags
$page = Page::factory()->create();
$page->attachTags(['laravel', 'php']);

// Create page hierarchy
$parent = Page::factory()->create();
$child = Page::factory()->create([
    'parent_id' => $parent->id,
]);
```

## Testing Filament Resources

### Testing Page Resource

```php
use Siteman\Cms\Resources\Pages\Pages\ListPages;
use Siteman\Cms\Resources\Pages\Pages\CreatePage;
use Siteman\Cms\Resources\Pages\Pages\EditPage;
use Siteman\Cms\Models\Page;
use function Pest\Livewire\livewire;

it('can list pages', function () {
    $pages = Page::factory()->count(5)->create();

    livewire(ListPages::class)
        ->assertCanSeeTableRecords($pages);
});

it('can create a page', function () {
    livewire(CreatePage::class)
        ->fillForm([
            'title' => 'New Page',
            'slug' => '/new-page',
            'type' => 'page',
            'published_at' => now(),
        ])
        ->call('create')
        ->assertNotified();

    assertDatabaseHas('pages', [
        'title' => 'New Page',
        'slug' => '/new-page',
    ]);
});

it('can edit a page', function () {
    $page = Page::factory()->create();

    livewire(EditPage::class, ['record' => $page->id])
        ->fillForm([
            'title' => 'Updated Title',
        ])
        ->call('save')
        ->assertNotified();

    expect($page->fresh()->title)->toBe('Updated Title');
});
```

### Testing Table Features

```php
it('can search pages', function () {
    $pages = Page::factory()->count(3)->create();

    livewire(ListPages::class)
        ->searchTable($pages->first()->title)
        ->assertCanSeeTableRecords([$pages->first()])
        ->assertCanNotSeeTableRecords($pages->skip(1));
});

it('can filter pages by type', function () {
    Page::factory()->create(['type' => 'page']);
    $blogIndex = Page::factory()->create(['type' => 'blog_index']);

    livewire(ListPages::class)
        ->filterTable('type', 'blog_index')
        ->assertCanSeeTableRecords([$blogIndex]);
});

it('can sort pages', function () {
    $pages = Page::factory()->count(3)->create();

    livewire(ListPages::class)
        ->sortTable('title')
        ->assertCanSeeTableRecords($pages->sortBy('title'), inOrder: true);
});
```

### Testing Actions

```php
use Siteman\Cms\Resources\Pages\Pages\EditPage;

it('can delete a page', function () {
    $page = Page::factory()->create();

    livewire(EditPage::class, ['record' => $page->id])
        ->callAction('delete');

    assertSoftDeleted('pages', ['id' => $page->id]);
});

it('can duplicate a page', function () {
    $page = Page::factory()->create();

    livewire(EditPage::class, ['record' => $page->id])
        ->callAction('duplicate');

    expect(Page::count())->toBe(2);
});
```

## Testing Custom Blocks

```php
use App\Blocks\VideoBlock;
use Siteman\Cms\Models\Page;

it('renders a video block', function () {
    $page = Page::factory()->create([
        'blocks' => [
            [
                'type' => 'video',
                'data' => [
                    'url' => 'https://youtube.com/watch?v=example',
                ],
            ],
        ],
    ]);

    get($page->computed_slug)
        ->assertOk()
        ->assertSee('youtube.com/watch?v=example');
});

it('validates video block schema', function () {
    $block = new VideoBlock();
    $schema = $block->getSchema();

    expect($schema)->toBeArray()
        ->and($schema)->not->toBeEmpty();
});
```

## Testing Custom Themes

```php
use App\Siteman\Themes\MyTheme;
use Siteman\Cms\Facades\Siteman;

it('registers theme correctly', function () {
    $theme = new MyTheme();

    expect($theme->getName())->toBe('my-theme');
});

it('registers menu locations', function () {
    $theme = new MyTheme();
    $theme->configure(Siteman::getFacadeRoot());

    $locations = Siteman::getMenuLocations();

    expect($locations)->toHaveKey('footer')
        ->and($locations['footer'])->toBe('Footer Menu');
});

it('uses correct view namespace', function () {
    config(['siteman.themes' => [MyTheme::class]]);

    expect(view()->exists('siteman.themes.my-theme.pages.show'))->toBeTrue();
});
```

## Testing Page Types

```php
use Siteman\Cms\PageTypes\BlogIndex;
use Siteman\Cms\Models\Page;

it('blog index displays child pages', function () {
    $blog = Page::factory()->create([
        'type' => 'blog_index',
        'computed_slug' => '/blog',
    ]);

    $posts = Page::factory()->count(3)->create([
        'parent_id' => $blog->id,
        'published_at' => now()->subDay(),
    ]);

    get('/blog')
        ->assertOk()
        ->assertSee($posts->first()->title);
});

it('blog index paginates posts', function () {
    $blog = Page::factory()->create(['type' => 'blog_index']);

    Page::factory()->count(15)->create([
        'parent_id' => $blog->id,
        'published_at' => now()->subDay(),
    ]);

    $response = get($blog->computed_slug);

    $response->assertOk();
    expect($response['posts'])->toHaveCount(10); // Default pagination
});
```

## Testing Tags

```php
use Siteman\Cms\Models\Page;
use Spatie\Tags\Tag;

it('attaches tags to pages', function () {
    $page = Page::factory()->create();

    $page->attachTags(['laravel', 'php']);

    expect($page->tags)->toHaveCount(2)
        ->and($page->tags->pluck('name')->toArray())->toContain('laravel', 'php');
});

it('displays tagged pages on tag index', function () {
    $tagIndex = Page::factory()->create([
        'type' => 'tag_index',
        'computed_slug' => '/tags',
    ]);

    $page = Page::factory()->published()->create();
    $page->attachTag('laravel');

    get('/tags/laravel')
        ->assertOk()
        ->assertSee($page->title);
});
```

## Testing Menus

```php
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\MenuItem;
use Siteman\Cms\Models\Page;

it('creates menu with items', function () {
    $menu = Menu::factory()->create(['name' => 'Primary']);
    $page = Page::factory()->create();

    $item = MenuItem::factory()->create([
        'menu_id' => $menu->id,
        'linkable_type' => Page::class,
        'linkable_id' => $page->id,
        'title' => 'Link to Page',
    ]);

    expect($menu->menuItems)->toHaveCount(1)
        ->and($item->linkable)->toBeInstanceOf(Page::class);
});

it('displays menu in correct location', function () {
    $menu = Menu::factory()->create();
    $menu->menuLocations()->create(['location' => 'primary']);

    $items = Siteman::getMenuItems('primary');

    expect($items)->not->toBeEmpty();
});
```

## Testing Permissions

```php
use App\Models\User;
use Siteman\Cms\Models\Page;
use Spatie\Permission\Models\Role;

it('authorizes page access', function () {
    $user = User::factory()->create();
    $page = Page::factory()->create();

    expect($user->can('view', $page))->toBeTrue();
});

it('restricts editing without permission', function () {
    $user = User::factory()->create();
    $page = Page::factory()->create();

    expect($user->can('update', $page))->toBeFalse();
});

it('super admin has all permissions', function () {
    $user = User::factory()->create();
    $role = Siteman::createSuperAdminRole();
    $user->assignRole($role);

    $page = Page::factory()->create();

    expect($user->can('update', $page))->toBeTrue()
        ->and($user->can('delete', $page))->toBeTrue();
});
```

## Testing RSS Feeds

```php
use Siteman\Cms\Models\Page;

it('generates rss feed', function () {
    $feed = Page::factory()->create([
        'type' => 'rss_feed',
        'computed_slug' => '/feed',
    ]);

    Page::factory()->count(5)->published()->create();

    get('/feed')
        ->assertOk()
        ->assertHeader('content-type', 'text/xml; charset=UTF-8');
});
```

## Authentication in Tests

### Acting as User

```php
use App\Models\User;

it('requires authentication to access admin', function () {
    get('/admin')->assertRedirect('/admin/login');
});

it('authenticated user can access admin', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/admin')
        ->assertOk();
});
```

### Testing with Specific Roles

```php
it('editor can create pages', function () {
    $user = User::factory()->create();
    $user->assignRole('editor');

    actingAs($user);

    livewire(CreatePage::class)
        ->assertSuccessful();
});
```

## Browser Testing (E2E)

```php
use function Pest\Laravel\{browse};

it('can create page through UI', function () {
    $user = User::factory()->create();

    browse(function ($browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/admin/pages')
            ->click('@new-page')
            ->type('title', 'My New Page')
            ->type('slug', '/my-new-page')
            ->press('Create')
            ->assertPathIs('/admin/pages')
            ->assertSee('My New Page');
    });
});
```

## Test Helpers

### Custom Assertions

Create custom assertions in `tests/Pest.php`:

```php
expect()->extend('toBePublished', function () {
    return $this->published_at !== null
        && $this->published_at->isPast();
});

// Usage
expect($page)->toBePublished();
```

### Setup Helpers

```php
// tests/Pest.php

function createAdminUser(): User
{
    $user = User::factory()->create();
    $user->assignRole(Siteman::createSuperAdminRole());
    return $user;
}

function createBlogWithPosts(int $count = 5): Page
{
    $blog = Page::factory()->create(['type' => 'blog_index']);
    Page::factory()->count($count)->create(['parent_id' => $blog->id]);
    return $blog;
}

// Usage in tests
it('displays blog posts', function () {
    $blog = createBlogWithPosts(10);
    // ...
});
```

## Continuous Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: dom, curl, libxml, mbstring, zip, pcntl, sqlite3
          coverage: xdebug

      - name: Install Dependencies
        run: composer install --no-interaction

      - name: Run Tests
        run: composer test

      - name: Upload Coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./coverage.xml
```

## Best Practices

1. **Test Coverage**: Aim for >80% code coverage on critical paths
2. **Test Isolation**: Each test should be independent
3. **Use Factories**: Always use factories instead of manual model creation
4. **Descriptive Names**: Use descriptive test names that explain what's being tested
5. **Arrange-Act-Assert**: Follow the AAA pattern
6. **Test Edge Cases**: Test boundary conditions and error states
7. **Fast Tests**: Keep tests fast by using in-memory databases
8. **Clean State**: Use database transactions to rollback changes

## Common Issues

### Tests Failing Due to Cache

Clear caches before running tests:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Database Issues

Ensure using in-memory SQLite for tests:

```php
// phpunit.xml or Pest.php
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Livewire Component Not Found

Ensure Filament assets are built:

```bash
php artisan filament:assets
```

## Related Documentation

- [Contributing](/contributing/) - Development guidelines
- [Pest PHP Documentation](https://pestphp.com/docs)
- [Filament Testing Documentation](https://filamentphp.com/docs/panels/testing)
