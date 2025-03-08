---
outline: deep
---

# Installation

You can install the package via composer:

```bash
composer require siteman/cms
```

You can install Siteman via its own artisan command. It is interactive and will also create the first user for you.

```bash
php artisan siteman:install
```

Normally the `siteman:install` command should take care of everything. If you want to install it manually, you can so
by enabling the Siteman plugin in your Filament panel.

```php
//...
$panel->plugin(SitemanPlugin::make());
//...
```

