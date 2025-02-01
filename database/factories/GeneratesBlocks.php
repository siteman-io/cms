<?php

namespace Siteman\Cms\Database\Factories;

trait GeneratesBlocks
{
    protected function makeBlocks(int $count = 1): array
    {
        $blocks = [];
        foreach (range(1, $count) as $index) {
            $blocks[] = [
                'type' => 'markdown-block',
                'data' => [
                    'content' => fake()->paragraphs(rand(3, 10), true),
                ],
            ];
        }

        return $blocks;
    }
}
