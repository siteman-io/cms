---
outline: deep
---

# Installation

## Prerequisites

Before installing Siteman CMS, ensure your environment meets these requirements:

- **PHP**: 8.3 or higher
- **Laravel**: 11.x or 12.x
- **Filament**: 4.x
- **Database**: MySQL 5.7+, PostgreSQL 10+, or SQLite 3.8+
- **Composer**: 2.x

## New Laravel Project

If you're starting a fresh Laravel project:

```bash
# Create a new Laravel project
composer create-project laravel/laravel my-siteman-project

cd my-siteman-project

# Install Filament
composer require filament/filament:"^4.0"

# Create a Filament panel
php artisan filament:install --panels
```

## Installing Siteman

Install Siteman via Composer:

```bash
composer require siteman/cms
```

## Automatic Setup (Recommended)

Run the interactive installation command. This will:
- Publish configuration files
- Run database migrations
- Create an admin user account
- Publish and build assets

```bash
php artisan siteman:install
```

Follow the prompts to create your admin user account.

## Manual Setup

If you prefer to set up Siteman manually or need more control:

### 1. Publish Configuration

```bash
php artisan vendor:publish --tag="siteman-config"
```

This publishes `config/siteman.php` where you can customize models, themes, and middleware.

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Publish Assets

```bash
php artisan siteman:publish
```

### 4. Register the Plugin

Add the Siteman plugin to your Filament panel (e.g., `app/Providers/Filament/AdminPanelProvider.php`):

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

### 5. Create an Admin User

```bash
php artisan siteman:create-admin
```

## Verification

Start your development server and verify the installation:

```bash
php artisan serve
```

Visit `http://localhost:8000/admin` and log in with your admin credentials.

## What Gets Installed

Siteman installation includes:

- **Database Tables**: pages, menus, menu_items, menu_locations, tags, and related tables
- **Filament Resources**: PageResource, MenuResource, UserResource, RoleResource
- **Configuration Files**: `config/siteman.php`
- **Views**: Theme templates in `resources/views/vendor/siteman`
- **Permissions**: Powered by Spatie Laravel Permission
- **Media Library**: Powered by Spatie Laravel Media Library

## Troubleshooting

### Installation Command Fails

If `php artisan siteman:install` fails:

1. Ensure all prerequisites are met (PHP version, Laravel version)
2. Check database connection in `.env`
3. Run migrations manually: `php artisan migrate`
4. Check file permissions for `storage/` and `bootstrap/cache/`

### Plugin Not Appearing in Admin

If the Siteman plugin doesn't appear:

1. Clear application cache: `php artisan optimize:clear`
2. Verify `SitemanPlugin::make()` is registered in your panel provider
3. Check the panel path matches (e.g., `/admin`)
4. Ensure the user has necessary permissions

### Frontend Pages Return 404

If created pages return 404:

1. Verify the catch-all route is registered (run `php artisan route:list`)
2. Ensure Siteman routes aren't conflicting with your application routes
3. Check the page's `computed_slug` in the database matches the URL
4. Verify the page is published (`published_at` is in the past)

### Asset Build Errors

If you encounter asset compilation errors:

```bash
# Clear compiled assets
php artisan view:clear
php artisan filament:clear-cache

# Rebuild Filament assets
php artisan filament:assets
```

### Permission Errors

If you get permission-related errors:

1. Run migrations to create permission tables: `php artisan migrate`
2. Assign roles to your user via the admin panel
3. Check `config/permission.php` is published: `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`

## Next Steps

- [Quick Start Guide](/getting-started/quick-start) - Create your first page in 5 minutes
- [Configuration](/getting-started/configuration) - Customize Siteman to your needs
- [Creating Themes](/features/themes) - Build custom themes for your site

