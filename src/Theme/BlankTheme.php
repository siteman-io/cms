<?php declare(strict_types=1);

namespace Siteman\Cms\Theme;

use Siteman\Cms\Models\BasePostType;
use Siteman\Cms\Siteman;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;

class BlankTheme implements ThemeInterface
{
    public function configure(Siteman $siteman): void
    {
        $siteman
            ->registerMenuLocation('header', 'Header')
            ->registerMenuLocation('footer', 'Footer');

        $siteman->registerLayout(BaseLayout::class);
    }

    public function render(BasePostType $post): View
    {
        return view('siteman::themes.blank.show', ['post' => $post]);
    }

    public function renderIndex(LengthAwarePaginator $collection): View
    {
        return view('siteman::themes.blank.index', ['collection' => $collection]);
    }
}
