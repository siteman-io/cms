<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Illuminate\Contracts\View\View;
use Siteman\Cms\Models\Post;
use Siteman\Cms\View\Renderer;

class ShowBlogIndex
{
    public function __construct(private readonly Renderer $renderer) {}

    public function __invoke(): View
    {
        $posts = Post::query()
            ->published()
            ->with('tags')
            ->orderBy('published_at')
            ->paginate(5);

        return $this->renderer->renderPostIndex($posts);
    }
}
