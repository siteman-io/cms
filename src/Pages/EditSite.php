<?php declare(strict_types=1);

namespace Siteman\Cms\Pages;

use Filament\Pages\Tenancy\EditTenantProfile;

class EditSite extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Edit Site';
    }
}
