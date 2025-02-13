<?php

namespace Siteman\Cms\Http;

use Illuminate\Support\Facades\Context;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Settings\BlogSettings;
use Spatie\Tags\Tag;

class TagsController
{
    public function show(string $slug)
    {
        abort_unless(BlogSettings::isEnabled(), 404);

        $tag = Tag::where('slug->en', $slug)->firstOrFail();
        Context::add('current_tag', $tag);
        $posts = Post::published()->withAnyTags([$tag])->paginate(5);

        return Siteman::theme()->renderIndex($posts);
    }
}
