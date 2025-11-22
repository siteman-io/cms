<?php declare(strict_types=1);

namespace Siteman\Cms\Pages\Concerns;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

trait IsProtectedPage
{
    public function booted()
    {
        if (!static::canAccess()) {

            Notification::make()
                ->title('forbidden')
                ->warning()
                ->send();

            redirect($this->getRedirectPath());

            return;
        }

        if (method_exists(parent::class, 'booted')) {
            parent::booted();
        }
    }

    protected function getRedirectPath(): string
    {
        return Filament::getUrl();
    }

    public static function getPermissionName(): string
    {
        return 'page_'.class_basename(static::class);
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::canAccess() && parent::shouldRegisterNavigation();
    }

    public static function canAccess(array $parameters = []): bool
    {
        return Filament::auth()->user()->can(static::getPermissionName());
    }
}
