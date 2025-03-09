---
outline: deep
---

# Menus

Menus are used to define the navigation of your site. Your theme need to provide menu locations. You can define the
locations in your Themes `configure` method.

```php
    public function configure(Siteman $siteman): void
    {
        //...
        $siteman->registerMenuLocation('header', __('menu.header'));
    }
```

Registered menu locations and their assigned menus can be found via the `Locations` action.

![siteman_menu_locations.png](../img/siteman_menu_locations.png)

Menus can be used in your Blade views via the `Siteman` facade.

```bladehtml

<ul>
    @foreach(\Siteman\Cms\Facades\Siteman::getMenuItems('header') as $item)
    <li><a href="{{$item->url}}" class="hover:underline">>{{$item->title}}</a></li>
    @endforeach
</ul>
```
