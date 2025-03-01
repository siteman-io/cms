<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Siteman\Cms\Models\Page;

class BlockRenderer
{
    public function __construct(protected readonly BlockRegistry $blockRegistry) {}

    public function render(array $block, Page $page)
    {
        $blockInstance = $this->blockRegistry->getById($block['type']);
        if (!$blockInstance) {
            return '';
        }

        return $blockInstance->render($block['data'], $page);
    }
}
