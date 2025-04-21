<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Filament\Forms\Components\Builder\Block;

abstract class BaseBlock implements BlockInterface
{
    abstract public function id(): string;

    public function getBlock(): Block
    {
        return Block::make($this->id())->schema($this->fields());
    }

    abstract protected function fields(): array;

    protected function getView(array $data, string $default): string
    {
        return $data['view'] ?? $default;
    }
}
