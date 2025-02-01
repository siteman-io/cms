@php
    /**
    * @var \Siteman\Cms\Models\BasePostType $post
    */
    $renderer = app(\Siteman\Cms\Blocks\BlockRenderer::class)
@endphp
@foreach($post->blocks ?? [] as $block)
    {!! $renderer->render($block, $post)  !!}
@endforeach
