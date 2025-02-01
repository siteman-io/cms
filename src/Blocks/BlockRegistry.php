<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Illuminate\Support\Collection;

class BlockRegistry
{
    public function __construct(protected Collection $blocks = new Collection) {}

    public function register(BlockInterface $block): self
    {
        $this->blocks->put($block->id(), $block);

        return $this;
    }

    public function all(): Collection
    {
        return $this->blocks;
    }

    public function raw(): array
    {
        return $this->blocks->toArray();
    }

    public function getBlockIds(): Collection
    {
        return $this->blocks->keys();
    }

    public function getById(string $id): ?BlockInterface
    {
        return $this->blocks->first(fn (BlockInterface $block) => $block->id() === $id);
    }
}
