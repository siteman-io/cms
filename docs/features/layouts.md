---
outline: deep
---

# Layouts

Layouts are Blade components that wrap page content. When you set a layout on a page, it takes over rendering instead of the default page view.

## How It Works

If a page has a layout set, Siteman renders it using `<x-dynamic-component>`:

```blade
<x-dynamic-component :component="$layout" :page="$page" />
```

If no layout is set, normal view resolution kicks in.

## Creating a Layout

A layout is just a Blade component. Here's the basic structure:

```php
<?php declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Siteman\Cms\Models\Page;

class MyLayout extends Component
{
    public function __construct(public Page $page) {}

    public static function getId(): string
    {
        return 'my-layout';
    }

    public function render()
    {
        return view('components.layouts.my-layout', [
            'page' => $this->page,
        ]);
    }
}
```

The view needs to render the page blocks:

```blade
{{-- resources/views/components/layouts/my-layout.blade.php --}}
@php
    $renderer = app(\Siteman\Cms\Blocks\BlockRenderer::class);
@endphp

<!DOCTYPE html>
<html>
<head>
    {!! seo()->for($page) !!}
    @vite(['resources/css/app.css'])
</head>
<body>
    <h1>{{ $page->title }}</h1>

    @foreach($page->blocks ?? [] as $block)
        {!! $renderer->render($block, $page) !!}
    @endforeach
</body>
</html>
```

## Registering Layouts

First register as a Blade component, then with Siteman:

```php
// AppServiceProvider
use Illuminate\Support\Facades\Blade;

public function boot(): void
{
    Blade::component('my-layout', MyLayout::class);
}
```

```php
// In your theme's configure method
public function configure(Siteman $siteman): void
{
    $siteman->registerLayout(MyLayout::class);
}
```

Once registered, layouts show up in the page form sidebar as a dropdown.

## Related

- [Themes](/features/themes)
- [Blocks](/features/blocks)
