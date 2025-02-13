<?php

namespace Siteman\Cms\Http;

use Illuminate\Support\Facades\Context;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Settings\BlogSettings;

class PostsController
{
    public function index()
    {
        abort_unless(BlogSettings::isEnabled(), 404);

        $posts = Post::query()
            ->published()
            ->with('tags')
            ->orderBy('published_at')
            ->paginate(5);

        return Siteman::theme()->renderIndex($posts);
    }

    public function show($slug)
    {
        abort_unless(BlogSettings::isEnabled(), 404);

        $post = Post::query()
            ->published()
            ->with('tags')
            ->where('slug', $slug)
            ->firstOrFail();
        Context::add('current_post', $post);

        return Siteman::theme()->render($post);
    }
}
