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
    <header>
        <ul>
            @foreach(\Siteman\Cms\Facades\Siteman::getMenuItems('header') as $item)
                <li><a href="{{$item->url}}" class="hover:underline">>{{$item->title}}</a></li>
            @endforeach
        </ul>
    </header>
    <main>
        @foreach($page->blocks ?? [] as $block)
            {!! $renderer->render($block, $page)  !!}
        @endforeach
    </main>
    <footer>
        <ul>
            @foreach(\Siteman\Cms\Facades\Siteman::getMenuItems('footer') as $item)
                <li><a href="{{$item->url}}" class="hover:underline">>{{$item->title}}</a></li>
            @endforeach
        </ul>
    </footer>
@endsection
