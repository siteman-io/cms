---
outline: deep
---

# Themes

Themes control how your frontend looks. A theme registers menu locations, layouts, blocks, and provides Blade views.

## Creating a Theme

```bash
php artisan make:siteman-theme MyTheme
```

This gives you a theme class and some starter views.

## Theme Structure

A theme implements `ThemeInterface` with two methods:

```php
<?php declare(strict_types=1);

namespace App\Siteman\Themes;

use Siteman\Cms\Siteman;
use Siteman\Cms\Theme\ThemeInterface;

class MyTheme implements ThemeInterface
{
    public static function getName(): string
    {
        return 'My Theme';
    }

    public function configure(Siteman $siteman): void
    {
        $siteman->registerMenuLocation('header', 'Header');
        $siteman->registerMenuLocation('footer', 'Footer');

        $siteman->registerLayout(MyLayout::class);
        $siteman->registerBlock(HeroBlock::class);
    }
}
```

Register it in `config/siteman.php`:

```php
'themes' => [
    \App\Siteman\Themes\MyTheme::class,
],
```

## View Structure

Put your views in `resources/views/siteman/themes/{theme-name}/`:

```
my-theme/
├── pages/
│   └── show.blade.php
├── posts/
│   └── index.blade.php
└── tags/
    └── show.blade.php
```

## View Resolution

Siteman looks for views in order and uses the first one that exists:

**Pages:**
1. `{theme}.pages.{slug}` (specific to that page)
2. `{theme}.pages.show` (generic)
3. `siteman::themes.blank.pages.show` (fallback)

**Blog:** `{theme}.posts.index` → fallback

**Tags:** `{theme}.tags.show` → fallback

## Optional: Panel Customization

You can customize the Filament admin panel from your theme:

```php
public function configurePanel(Panel $panel): void
{
    $panel
        ->brandName('My Site')
        ->colors(['primary' => Color::Blue]);
}
```

## Package Themes

If you're distributing a theme as a package, add a `getViewPrefix()` method:

```php
public function getViewPrefix(): string
{
    return 'my-package::themes.my-theme';
}
```

## Related

- [Blocks](/features/blocks)
- [Layouts](/features/layouts)
- [Menus](/features/menus)
