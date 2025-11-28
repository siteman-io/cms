---
outline: deep
---

# Page Types

Page types control how a page behaves and renders. Siteman ships with four built-in types.

## Built-in Types

### Page

The default. Uses blocks for content with optional layout override.

Views are resolved in this order:
1. Layout component (if set)
2. `{theme}.pages.{slug}`
3. `{theme}.pages.show`
4. `siteman::themes.blank.pages.show`

### Blog Index

Lists child pages as blog posts. Create a page with this type, then add child pages as your posts.

Passes a `$posts` collection to the view with 10 items per page.

### Tag Index

Shows pages filtered by tag. Handles both the tag listing (`/tags`) and individual tag pages (`/tags/{slug}`).

### RSS Feed

Generates an Atom feed. You can configure the title, description, and language in the page form.

## Creating Custom Page Types

::: warning
Custom page type registration is still a bit rough. You'll need to look at how the built-in types work and extend from there.
:::

A page type implements `PageTypeInterface`:

```php
<?php declare(strict_types=1);

namespace App\PageTypes;

use Illuminate\Http\Request;
use Siteman\Cms\Models\Page;
use Siteman\Cms\PageTypes\PageTypeInterface;
use Siteman\Cms\PageTypes\Concerns\InteractsWithPageForm;
use Siteman\Cms\PageTypes\Concerns\InteractsWithViews;

class MyPageType implements PageTypeInterface
{
    use InteractsWithPageForm;
    use InteractsWithViews;

    public function render(Request $request, Page $page)
    {
        return $this->renderView(
            [$this->getViewPath('my-type.show')],
            ['page' => $page],
        );
    }
}
```

## Extending Page Forms

Page types can add fields to the page form. Override these methods:

```php
// Add to main content area
public static function extendPageMainFields(array $fields): array
{
    return array_merge($fields, [
        TextInput::make('subtitle'),
    ]);
}

// Add to sidebar
public static function extendPageSidebarFields(array $fields): array
{
    return array_merge($fields, [
        Toggle::make('show_sidebar')->default(true),
    ]);
}
```

Use `->asPageMetaField()` to store values in the page's `meta` JSON column:

```php
TextInput::make('custom_field')->asPageMetaField()
```

Then retrieve with `$page->getMeta('custom_field', 'default')`.

## Related

- [Blocks](/features/blocks)
- [Themes](/features/themes)
- [Blog](/features/blog)
