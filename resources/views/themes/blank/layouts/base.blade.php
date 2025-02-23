@extends('siteman::themes.blank.wrapper')
@php
    /**
    * @var \Siteman\Cms\Models\Page $page
    */
    $renderer = app(\Siteman\Cms\Blocks\BlockRenderer::class)
@endphp

@section('seo')
    {!! seo()->for($page) !!}
@endsection

@section('content')
    <main>
        @foreach($page->blocks ?? [] as $block)
            {!! $renderer->render($block, $page)  !!}
        @endforeach
    </main>
@endsection
