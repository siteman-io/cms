<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Settings\BlogSettings;
use Spatie\Tags\Tag;

class ShowTag
{
    public function __invoke(Request $request, BlogSettings $blogSettings)
    {
        $tag = Tag::where('slug->en', str_replace($blogSettings->tag_route_prefix, '', $request->path()))->firstOrFail();
        Context::add('current_tag', $tag);
        $posts = Post::published()->withAnyTags([$tag])->paginate(5);

        return Siteman::theme()->renderIndex($posts);
    }
}
