<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Models\Tag;
use Siteman\Cms\Tests\Fixtures\DummyTheme;
use Siteman\Cms\View\Renderer;

beforeEach(function () {
    $this->viewFactory = mock(Factory::class);
    $this->theme = new DummyTheme;
    $this->renderer = new Renderer($this->theme, $this->viewFactory);
});

it('renders pages with their layout', function () {
    $page = new Page;
    $page->layout = 'test-layout';
    $this->viewFactory
        ->shouldReceive('make')
        ->with('siteman::themes.layout', ['page' => $page, 'layout' => 'test-layout'])
        ->andReturn($view = mock(View::class));

    expect($this->renderer->renderPostType($page))->toBe($view);
});

it('renders pages without layout on a defined view cascade', function () {
    $page = new Page;
    $page->slug = 'test-page';
    $this->viewFactory
        ->shouldReceive('exists')
        ->with('themes.dummy.pages.test-page')
        ->andReturn(false)
        ->shouldReceive('exists')
        ->with('themes.dummy.pages.show')
        ->andReturn(false)
        ->shouldReceive('exists')
        ->with('siteman::themes.blank.pages.show')
        ->andReturn(true);
    $this->viewFactory
        ->shouldReceive('make')
        ->with('siteman::themes.blank.pages.show', ['page' => $page])
        ->andReturn($view = mock(View::class));

    expect($this->renderer->renderPostType($page))->toBe($view);
});

it('renders posts on a defined view cascade', function () {
    $post = new Post;
    $post->slug = 'test-post';
    $this->viewFactory
        ->shouldReceive('exists')
        ->with('themes.dummy.posts.test-post')
        ->andReturn(false)
        ->shouldReceive('exists')
        ->with('themes.dummy.posts.show')
        ->andReturn(false)
        ->shouldReceive('exists')
        ->with('siteman::themes.blank.posts.show')
        ->andReturn(true);
    $this->viewFactory
        ->shouldReceive('make')
        ->with('siteman::themes.blank.posts.show', ['post' => $post])
        ->andReturn($view = mock(View::class));

    expect($this->renderer->renderPostType($post))->toBe($view);
});

it('renders tags', function () {
    $tag = new Tag;
    $tag->slug = 'test-tag';
    $posts = mock(LengthAwarePaginator::class);
    $this->viewFactory
        ->shouldReceive('exists')
        ->with('themes.dummy.tags.test-tag')
        ->andReturn(false)
        ->shouldReceive('exists')
        ->with('themes.dummy.tags.show')
        ->andReturn(false)
        ->shouldReceive('exists')
        ->with('siteman::themes.blank.tags.show')
        ->andReturn(true);
    $this->viewFactory
        ->shouldReceive('make')
        ->with('siteman::themes.blank.tags.show', ['tag' => $tag, 'posts' => $posts])
        ->andReturn($view = mock(View::class));

    expect($this->renderer->renderTag($tag, $posts))->toBe($view);
});

it('renders post index page', function () {
    $posts = mock(LengthAwarePaginator::class);
    $this->viewFactory
        ->shouldReceive('exists')
        ->with('themes.dummy.posts.index')
        ->andReturn(false)
        ->shouldReceive('exists')
        ->with('siteman::themes.blank.posts.index')
        ->andReturn(true);
    $this->viewFactory
        ->shouldReceive('make')
        ->with('siteman::themes.blank.posts.index', ['posts' => $posts])
        ->andReturn($view = mock(View::class));

    expect($this->renderer->renderPostIndex($posts))->toBe($view);
});
