<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Siteman\Cms\Models\BasePostType;

class BlockRenderer
{
    public function __construct(protected readonly BlockRegistry $blockRegistry) {}

    public function render(array $block, BasePostType $post)
    {
        $blockInstance = $this->blockRegistry->getById($block['type']);
        if (!$blockInstance) {
            return '';
        }

        return $blockInstance->render($block['data'], $post);
    }
}
