<?php

declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource;

use Filament\Support\Contracts\HasLabel;

enum LinkTarget: string implements HasLabel
{
    case Self = '_self';

    case Blank = '_blank';

    case Parent = '_parent';

    case Top = '_top';

    public function getLabel(): string
    {
        return match ($this) {
            self::Self => __('siteman::menu.open_in.options.self'),
            self::Blank => __('siteman::menu.open_in.options.blank'),
            self::Parent => __('siteman::menu.open_in.options.parent'),
            self::Top => __('siteman::menu.open_in.options.top'),
        };
    }
}
