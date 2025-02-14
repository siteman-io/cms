<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Illuminate\Http\Request;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Settings\BlogSettings;

class ShowBlogPost
{
    public function __invoke(Request $request, BlogSettings $blogSettings)
    {
        $post = Post::query()
            ->published()
            ->with('tags')
            ->where('slug', str_replace($blogSettings->getBlogIndexRoute(), '', $request->path()))
            ->firstOrFail();

        return Siteman::theme()->render($post);
    }
}
