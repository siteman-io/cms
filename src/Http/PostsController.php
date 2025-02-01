<?php

namespace Siteman\Cms\Http;

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Post;
use Illuminate\Support\Facades\Context;

class PostsController
{
    public function index()
    {
        $posts = Post::query()
            ->published()
            ->with('tags')
            ->orderBy('published_at')
            ->paginate(5);

        return Siteman::theme()->renderIndex($posts);
    }

    public function show($slug)
    {
        $post = Post::query()
            ->published()
            ->with('tags')
            ->where('slug', $slug)
            ->firstOrFail();
        Context::add('current_post', $post);

        return Siteman::theme()->render($post);
    }
}
