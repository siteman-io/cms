<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Siteman\Cms\Models\BasePostType;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

interface BlockInterface
{
    public function id(): string;

    public function getBlock(): Block;

    public function render(array $data, BasePostType $post): string|View|Htmlable;
}
