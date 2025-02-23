<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Illuminate\Contracts\View\View;
use Siteman\Cms\Models\Tag;
use Siteman\Cms\View\Renderer;

class ShowTagIndex
{
    public function __construct(private readonly Renderer $renderer) {}

    public function __invoke(): View
    {
        $tags = Tag::withCount('posts')->orderBy('slug->en')->paginate();

        return $this->renderer->renderTagIndex($tags);
    }
}
