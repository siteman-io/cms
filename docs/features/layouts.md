---
outline: deep
---

# Layouts

Layouts are the wrapper around your content. They define the structure of your page. Siteman ships with a default
`base-layout`. A Layout is nothing more than a Blade component. It will get post or page passed as a `post` property.
A new layout can be registered via your Themes `configure` method.

```php
    public function configure(Siteman $siteman): void
    {
        //...
        $siteman->registerLayout(BaseLayout::class);
    }
```

> [!IMPORTANT]  
> Layouts have to be registered as Blade components by your package/application. This
> is done by calling `Blade::component` method to your package's service provider.
