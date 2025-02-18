<?php declare(strict_types=1);

namespace Siteman\Cms\Theme;

use Siteman\Cms\Siteman;

interface ThemeInterface
{
    public static function getName(): string;

    public function configure(Siteman $siteman): void;
}
