<?php declare(strict_types=1);

namespace Siteman\Cms\Theme;

use Siteman\Cms\Models\BasePostType;
use Siteman\Cms\Siteman;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ThemeInterface
{
    public function configure(Siteman $siteman): void;

    public function render(BasePostType $post);

    public function renderIndex(LengthAwarePaginator $collection);
}
