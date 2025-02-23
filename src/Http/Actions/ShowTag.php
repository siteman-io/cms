<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Models\Tag;
use Siteman\Cms\Settings\BlogSettings;
use Siteman\Cms\View\Renderer;

class ShowTag
{
    public function __construct(private readonly Renderer $renderer) {}

    public function __invoke(Request $request, BlogSettings $blogSettings): View
    {
        $slug = str_replace($blogSettings->tag_index_route.'/', '', $request->path());
        $tag = Tag::where('slug->en', $slug)->firstOrFail();
        Context::add('current_tag', $tag);
        $posts = Post::published()->withAnyTags([$tag])->paginate(5);

        return $this->renderer->renderTag($tag, $posts);
    }
}
