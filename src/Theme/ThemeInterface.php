<?php declare(strict_types=1);

namespace Siteman\Cms\Theme;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Siteman\Cms\Models\BasePostType;
use Siteman\Cms\Siteman;

interface ThemeInterface
{
    public static function getName(): string;

    public function configure(Siteman $siteman): void;

    public function render(BasePostType $post);

    public function renderIndex(LengthAwarePaginator $collection);
}
