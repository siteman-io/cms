<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Post;

class ShowBlogIndex
{
    public function __invoke()
    {
        $posts = Post::query()
            ->published()
            ->with('tags')
            ->orderBy('published_at')
            ->paginate(5);

        return Siteman::theme()->renderIndex($posts);
    }
}
