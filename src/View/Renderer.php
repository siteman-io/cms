<?php declare(strict_types=1);

namespace Siteman\Cms\View;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Siteman\Cms\Models\BasePostType;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Models\Tag;
use Siteman\Cms\Theme\ThemeInterface;

class Renderer
{
    public function __construct(private readonly ThemeInterface $theme, private readonly Factory $view) {}

    public function renderPostType(BasePostType $post): View
    {
        if ($post instanceof Page) {
            if ($post->layout) {
                return $this->renderLayout($post->layout, ['page' => $post]);
            }

            return $this->render(
                [
                    $this->getViewPath('pages.'.($post->slug !== '/' ? str($post->slug)->replace('/', '.')->toString() : 'home')),
                    $this->getViewPath('pages.show'),
                    'siteman::themes.blank.pages.show',
                ],
                ['page' => $post],
            );
        }

        return $this->render(
            [
                $this->getViewPath('posts.'.str($post->slug)->replace('/', '.')),
                $this->getViewPath('posts.show'),
                'siteman::themes.blank.posts.show',
            ],
            ['post' => $post],
        );
    }

    public function renderTag(Tag $tag, LengthAwarePaginator $posts): View
    {
        return $this->render(
            [
                $this->getViewPath('tags.'.str($tag->slug)->replace('/', '.')->toString()),
                $this->getViewPath('tags.show'),
                'siteman::themes.blank.tags.show',
            ],
            [
                'tag' => $tag,
                'posts' => $posts,
            ],
        );
    }

    public function renderTagIndex(LengthAwarePaginator $tags): View
    {
        return $this->render(
            [
                $this->getViewPath('tags.index'),
                'siteman::themes.blank.tags.index',
            ],
            ['tags' => $tags],
        );
    }

    public function renderPostIndex(LengthAwarePaginator $posts): View
    {
        return $this->render(
            [
                $this->getViewPath('posts.index'),
                'siteman::themes.blank.posts.index',
            ],
            ['posts' => $posts],
        );
    }

    protected function getViewPath(string $view): string
    {
        $prefix = method_exists($this->theme, 'getViewPrefix')
            ? $this->theme->getViewPrefix()
            : Str::of(class_basename($this->theme))->before('Theme')->kebab()->prepend('themes.');

        return $prefix.'.'.$view;
    }

    protected function render(string|array $views, array $data = []): View
    {
        $views = Arr::wrap($views);

        foreach ($views as $view) {
            if ($this->view->exists($view)) {
                return $this->view->make($view, $data);
            }
        }
        throw new \Exception('No view found for the keys: '.implode(', ', $views));
    }

    protected function renderLayout(?string $layout, array $data = []): View
    {
        return $this->view->make(
            'siteman::themes.layout',
            array_merge($data, ['layout' => $layout ?? 'base-layout']),
        );
    }
}
