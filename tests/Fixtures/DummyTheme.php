<?php declare(strict_types=1);

namespace Siteman\Cms\Tests\Fixtures;

use Siteman\Cms\Siteman;
use Siteman\Cms\Theme\ThemeInterface;

class DummyTheme implements ThemeInterface
{
    public static function getName(): string
    {
        return 'Dummy Theme';
    }

    public function configure(Siteman $siteman): void {}
}
