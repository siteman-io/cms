<?php

namespace Siteman\Cms\Http;

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Post;
use Illuminate\Support\Facades\Context;
use Spatie\Tags\Tag;

class TagsController
{
    public function show(string $slug)
    {
        $tag = Tag::where('slug->en', $slug)->firstOrFail();
        Context::add('current_tag', $tag);
        $posts = Post::published()->withAnyTags([$tag])->paginate(5);

        return Siteman::theme()->renderIndex($posts);
    }
}
