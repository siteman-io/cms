---
outline: deep
---

# Configuration

Siteman CMS provides various configuration options to customize its behavior. After installation, you'll find the main configuration file at `config/siteman.php`.

## Publishing Configuration

If you haven't already, publish the configuration file:

```bash
php artisan vendor:publish --tag="siteman-config"
```

## Configuration Options

### Models

Customize which models Siteman uses for users:

```php
'models' => [
    'user' => 'App\Models\User',
],
```

This allows you to use your own User model with custom attributes and relationships.

### Themes

Register all available themes for your application:

```php
'themes' => [
    \Siteman\Cms\Theme\BlankTheme::class,
    \App\Siteman\Themes\MyCustomTheme::class,
],
```

The first theme in the array is used as the default. You can add multiple themes and switch between them per page or globally.

**Learn more**: [Creating Themes](/features/themes)

### Middleware

Configure middleware applied to all Siteman frontend routes:

```php
'middleware' => [
    'web',
    \Siteman\Cms\Http\Middleware\InjectAdminBar::class,
],
```

The `InjectAdminBar` middleware adds an admin toolbar to pages when you're logged in, providing quick links to edit content.

**Common customizations**:

```php
'middleware' => [
    'web',
    \Siteman\Cms\Http\Middleware\InjectAdminBar::class,
    \App\Http\Middleware\TrackPageViews::class, // Add analytics
    \App\Http\Middleware\CachePages::class,     // Add caching
],
```

## Panel Configuration

Siteman integrates with Filament panels. Configure your panel in `app/Providers/Filament/AdminPanelProvider.php`:

```php
use Siteman\Cms\SitemanPlugin;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('admin')
        ->login()
        ->colors([
            'primary' => Color::Amber,
        ])
        ->plugin(SitemanPlugin::make())
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->pages([
            Pages\Dashboard::class,
        ])
        ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ])
        ->authMiddleware([
            Authenticate::class,
        ]);
}
```

### Key Panel Options

**Panel Path**: Change where your admin panel is accessed:
```php
->path('dashboard') // Access at /dashboard instead of /admin
```

**Login Customization**: Customize the login page:
```php
->login(\App\Filament\Pages\CustomLogin::class)
```

**Colors**: Change the primary color:
```php
use Filament\Support\Colors\Color;

->colors([
    'primary' => Color::Blue,
])
```

**Branding**: Add your logo and custom branding:
```php
->brandName('My Site')
->brandLogo(asset('images/logo.svg'))
->darkMode(false)
```

## Database Configuration

Siteman uses your default database connection. If you need to use a different connection:

```php
// In a service provider
use Siteman\Cms\Models\Page;

Page::query()->connection('siteman');
```

## Media Library Configuration

Siteman uses Spatie's Media Library for file uploads. Configure it in `config/media-library.php`:

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"
```

**Common configurations**:

```php
// config/media-library.php

// Use S3 for storage
'disk_name' => 's3',

// Max file size (in KB)
'max_file_size' => 1024 * 10, // 10MB

// Path generator
'path_generator' => \Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,
```

**Learn more**: [Spatie Media Library Documentation](https://spatie.be/docs/laravel-medialibrary)

## Permissions Configuration

Siteman uses Spatie's Laravel Permission for role-based access control. Configure it in `config/permission.php`:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

**Customize permission models**:

```php
// config/permission.php

'models' => [
    'permission' => Spatie\Permission\Models\Permission::class,
    'role' => Spatie\Permission\Models\Role::class,
],
```

**Learn more**: [Managing Roles and Permissions](/features/settings)

## SEO Configuration

Siteman includes `ralphjsmit/laravel-seo` for SEO management. Publish its configuration:

```bash
php artisan vendor:publish --provider="RalphJSmit\Laravel\SEO\SEOServiceProvider"
```

**Customize SEO defaults**:

```php
// config/seo.php

'site_name' => 'My Awesome Site',
'title' => [
    'suffix' => ' | My Site',
    'prefix' => '',
],
```

## Caching Configuration

For production environments, consider caching:

**Route Caching**:
```bash
php artisan route:cache
```

**View Caching**:
```bash
php artisan view:cache
```

**Configuration Caching**:
```bash
php artisan config:cache
```

::: warning
When caching is enabled, you must clear the cache after making configuration changes:
```bash
php artisan optimize:clear
```
:::

## Environment Variables

Key environment variables for Siteman:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siteman
DB_USERNAME=root
DB_PASSWORD=

# Application
APP_NAME="My Siteman Site"
APP_URL=http://localhost

# File Storage (for Media Library)
FILESYSTEM_DISK=local
# Or for production:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=
```

## Advanced Configuration

### Custom Page Types

Register custom page types in your `AppServiceProvider`:

```php
use Siteman\Cms\PageTypes\PageTypeInterface;

public function boot(): void
{
    app('siteman.page-types')->register(
        'custom',
        \App\PageTypes\CustomPageType::class
    );
}
```

### Custom Blocks

Register custom blocks in your `AppServiceProvider`:

```php
use Siteman\Cms\Blocks\BlockRegistry;

public function boot(): void
{
    BlockRegistry::register('my-block', \App\Blocks\MyBlock::class);
}
```

**Learn more**: [Creating Custom Blocks](/features/blocks)

### Route Configuration

Siteman automatically registers a catch-all route for frontend pages. To customize route configuration, you can disable auto-registration and register routes manually:

```php
// In your RouteServiceProvider or routes/web.php

Route::middleware(config('siteman.middleware'))
    ->group(function () {
        Route::get('{slug}', [\Siteman\Cms\Http\SitemanController::class, 'show'])
            ->where('slug', '.*')
            ->name('siteman.page');
    });
```

## Next Steps

- [Create your first page](/getting-started/quick-start)
- [Explore page types](/features/page-types)
- [Customize themes](/features/themes)
- [Configure menus](/features/menus)
