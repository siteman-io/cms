<?php declare(strict_types=1);

namespace Siteman\Cms\Database\Factories;

use Illuminate\Support\Str;

trait GeneratesBlocks
{
    public function withMarkdownBlock(bool $showToc = false, ?array $elements = null): static
    {
        return $this->state(fn (array $attributes) => [
            'blocks' => [
                [
                    'type' => 'markdown-block',
                    'data' => [
                        'content' => implode("\n\n", $elements ?: [
                            fake()->markdownH1(),
                            fake()->markdownP(),
                            fake()->markdownH2(),
                            fake()->markdownBlockquote(),
                            fake()->markdownP(),
                            fake()->markdownH2(),
                            fake()->markdownP(),
                            fake()->markdownP(),
                            fake()->markdownH3(),
                            fake()->markdownP(),
                            fake()->markdownBlockquote(),
                            fake()->markdownH3(),
                            fake()->markdownP(),
                            fake()->markdownBulletedList(),
                            fake()->markdownH3(),
                            fake()->markdownP(),
                            fake()->markdownH2(),
                            fake()->markdownP(),
                            fake()->markdownH3(),
                            fake()->markdownP(),
                            fake()->markdownH3(),
                            fake()->markdownP(),
                            fake()->markdownH3(),
                            fake()->markdownP(),
                        ]),
                        'show_toc' => $showToc,
                    ],
                ],
            ],
        ]);
    }

    public function withBlogPost()
    {
        $blogPost = fake()->blogPost();

        return $this->state(fn (array $attributes) => [
            'title' => $blogPost->title,
            'slug' => '/'.Str::slug($blogPost->title),
            'blocks' => [
                [
                    'type' => 'markdown-block',
                    'data' => [
                        'content' => $blogPost->content,
                        'show_toc' => false,
                    ],
                ],
            ],
        ]);
    }
}
