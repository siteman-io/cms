<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Filament\Forms\Components\Builder\Block;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Siteman\Cms\Models\Page;

interface BlockInterface
{
    public function id(): string;

    public function getBlock(): Block;

    public function render(array $data, Page $page): string|View|Htmlable;
}
