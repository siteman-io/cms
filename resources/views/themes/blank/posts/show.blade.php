@extends('siteman::themes.blank.wrapper')
@php
    /**
    * @var \Siteman\Cms\Models\Post $post
    */
    $renderer = app(\Siteman\Cms\Blocks\BlockRenderer::class)
@endphp

@section('seo')
    {!! seo()->for($post) !!}
@endsection

@section('content')

    <main>
        @foreach($post->blocks ?? [] as $block)
            {!! $renderer->render($block, $post)  !!}
        @endforeach
    </main>

@endsection
