---
outline: deep
---

# Themes

Siteman ships with a default BlankTheme, which is not very exciting. But you can easily create your own theme by
executing
`php artisan make:siteman-theme`. This will create a new theme in the `App\Themes` namespace of your application
alongside some basic views to kickstart your theme development.

The theme can be enabled via your `config/siteman.php` file.

```php 
return [
    // ...
    'themes' => [
        \Siteman\Cms\Theme\BlankTheme::class,
    ],
    // ...
];
``` 

A Siteman theme is a PHP class which implements the `Siteman\Cms\Theme\ThemeInterface`. It defines two methods:

## `getName` method

The `getName` method is used to return a human-readable name of the theme.

## `configure` method

The Themes `configure` method is used to define the theme's configuration. It gets the `Siteman\Cms\Siteman` instance as
a dependency, which allows for easy access and manipulation of the Siteman configuration.

> [!IMPORTANT]  
> If you are proving a theme via a composer package you need to implement a `getViewPrefix` method.

## Default view files

Siteman renders different content through different cascades of view options. The first existing one is taken.

* Pages
    1. Layout if set on the Page
    2. `{theme}.pages.{slug}`
    3. `{theme}.pages.show`
    4. `siteman::themes.blank.pages.show`
* Posts
    1. `{theme}.posts.{slug}`
    2. `{theme}.posts.show`
    3. `siteman::themes.blank.posts.show`
* Post Index
    1. `{theme}.posts.index`
    2. `siteman::themes.blank.posts.index`
* Tags
    1. `{theme}.tags.{slug}`
    2. `{theme}.tags.show`
    3. `siteman::themes.blank.tags.show`
