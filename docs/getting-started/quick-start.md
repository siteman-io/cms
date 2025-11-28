---
outline: deep
---

# Quick Start

Get Siteman CMS up and running in 5 minutes.

## Prerequisites

- PHP 8.3 or higher
- Laravel 11 or 12
- Composer

## Installation

Install Siteman via Composer:

```bash
composer require siteman/cms
```

## Setup

Run the installation command to set up database tables, create an admin user, and publish assets:

```bash
php artisan siteman:install
```

Follow the prompts to create your admin user account.

## Register the Plugin

Add the Siteman plugin to your Filament panel in `app/Providers/Filament/AdminPanelProvider.php`:

```php
use Siteman\Cms\SitemanPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('admin')
        ->plugin(SitemanPlugin::make())
        // ... other configuration
}
```

## Create Your First Page

1. Start your development server:
```bash
php artisan serve
```

2. Visit the admin panel at `http://localhost:8000/admin`

3. Log in with your admin credentials

4. Navigate to **Content** > **Pages**

5. Click **New Page** and create your first page:
   - **Title**: "Welcome"
   - **Slug**: "/welcome"
   - **Type**: Page
   - **Published At**: Select current date/time

6. Add content using blocks (try the Markdown block)

7. Click **Create** to save your page

8. Visit `http://localhost:8000/welcome` to see your page!

## Create a Custom Theme

Generate a new theme to customize how your pages look:

```bash
php artisan make:siteman-theme MyTheme
```

This creates a theme class in `app/Siteman/Themes/MyTheme.php` and corresponding view files in `resources/views/siteman/themes/my-theme/`.

Register your theme in `config/siteman.php`:

```php
'themes' => [
    \App\Siteman\Themes\MyTheme::class,
],
```

## Next Steps

- [Explore page types](/features/page-types) to create blogs and tag indexes
- [Customize themes](/features/themes) to match your design
- [Add custom blocks](/features/blocks) for rich content
- [Configure menus](/features/menus) for site navigation
- [Set up layouts](/features/layouts) for different page templates
