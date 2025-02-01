<?php declare(strict_types=1);

namespace Siteman\Cms\Resources;

use BezhanSalleh\FilamentShield\Resources\RoleResource as FilamentShieldRoleResource;

class RoleResource extends FilamentShieldRoleResource
{
    public static function getNavigationGroup(): ?string
    {
        return __('siteman::resources/role.navigation-group');
    }

    public static function getNavigationIcon(): string
    {
        return __('siteman::resources/role.navigation-icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::resources/role.navigation-label');
    }

    public static function getLabel(): string
    {
        return self::getNavigationLabel();
    }
}
