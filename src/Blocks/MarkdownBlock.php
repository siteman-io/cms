<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use RyanChandler\CommonmarkBladeBlock\BladeExtension;
use Siteman\Cms\Models\BasePostType;
use Siteman\Cms\Torchlight\TorchlightExtension;

class MarkdownBlock extends BaseBlock
{
    public function id(): string
    {
        return 'markdown-block';
    }

    protected function fields(): array
    {
        return [
            MarkdownEditor::make('content'),
        ];
    }

    public function render(array $data, BasePostType $post): View
    {
        $extensions = [new BladeExtension, new AttributesExtension];
        if (config('torchlight.token') !== null) {
            $extensions[] = new TorchlightExtension;
        }

        $html = Str::markdown($data['content'], extensions: $extensions);

        return \view('siteman::blocks.markdown-block', ['content' => $html]);
    }
}
