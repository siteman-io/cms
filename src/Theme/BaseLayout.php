<?php declare(strict_types=1);

namespace Siteman\Cms\Theme;

use Illuminate\View\Component;
use Siteman\Cms\Models\Page;

class BaseLayout extends Component
{
    public function __construct(protected Page $page) {}

    public static function getId()
    {
        return 'base-layout';
    }

    public function render()
    {
        return $this->view('siteman::themes.blank.layouts.base', ['page' => $this->page]);
    }
}
