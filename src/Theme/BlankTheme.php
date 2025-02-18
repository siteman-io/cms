<?php declare(strict_types=1);

namespace Siteman\Cms\Theme;

use Siteman\Cms\Siteman;

class BlankTheme implements ThemeInterface
{
    public static function getName(): string
    {
        return 'Blank Theme';
    }

    public function configure(Siteman $siteman): void
    {
        $siteman
            ->registerMenuLocation('header', 'Header')
            ->registerMenuLocation('footer', 'Footer');

        $siteman->registerLayout(BaseLayout::class);
    }

    public function getViewPrefix(): string
    {
        return 'siteman::themes.blank';
    }
}
