<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Illuminate\Contracts\View\View;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Node\Query;
use League\CommonMark\Renderer\HtmlRenderer;
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
            MarkdownEditor::make('content')
                ->label(__('siteman::blocks.markdown.content.label'))
                ->helperText(__('siteman::blocks.markdown.content.helper-text')),
            Section::make(__('siteman::blocks.markdown.advanced'))
                ->collapsed()
                ->schema([
                    Toggle::make('show_toc')
                        ->label(__('siteman::blocks.markdown.show-toc.label'))
                        ->helperText(__('siteman::blocks.markdown.show-toc.helper-text')),
                ]),
        ];
    }

    public function render(array $data, BasePostType $post): View
    {
        $extensions = [new BladeExtension, new AttributesExtension];
        if (config('torchlight.token') !== null) {
            $extensions[] = new TorchlightExtension;
        }
        $options = [];
        $showToc = $data['show_toc'] ?? false;
        if ($showToc) {
            $extensions[] = new TableOfContentsExtension;
            $extensions[] = new HeadingPermalinkExtension;
            $options['heading_permalink'] = [
                'apply_id_to_heading' => true,
                'symbol' => '',
            ];
            $options['table_of_contents'] = [
                'html_class' => 'table-of-contents',
                'position' => 'top',
                'style' => 'bullet',
                'min_heading_level' => 1,
                'max_heading_level' => 6,
                'normalize' => 'relative',
            ];
        }

        $converter = new GithubFlavoredMarkdownConverter($options);
        $environment = $converter->getEnvironment();

        foreach ($extensions as $extension) {
            $environment->addExtension($extension);
        }

        $converted = $converter->convert($data['content']);

        $document = $converted->getDocument();

        if (!$showToc) {
            $renderer = new HtmlRenderer($environment);
            $html = $renderer->renderDocument($document);

            return \view('siteman::blocks.markdown-block', ['content' => $html, 'showToc' => $showToc]);
        }
        $toc = (new Query)
            ->where(Query::type(TableOfContents::class))
            ->findOne($document);

        $toc->detach();

        $renderer = new HtmlRenderer($environment);
        $html = $renderer->renderDocument($document);
        $toc = $renderer->renderNodes([$toc]);

        return \view('siteman::blocks.markdown-block', ['content' => $html, 'showToc' => $showToc, 'toc' => $toc]);
    }
}
