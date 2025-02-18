<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Settings\BlogSettings;
use Siteman\Cms\View\Renderer;

class ShowBlogPost
{
    public function __construct(private readonly Renderer $renderer) {}

    public function __invoke(Request $request, BlogSettings $blogSettings): View
    {
        $post = Post::query()
            ->published()
            ->with('tags')
            ->where('slug', str_replace($blogSettings->blog_index_route.'/', '', $request->path()))
            ->firstOrFail();

        return $this->renderer->renderPostType($post);
    }
}
