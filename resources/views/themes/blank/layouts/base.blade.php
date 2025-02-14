@php
    /**
    * @var \Siteman\Cms\Models\BasePostType $post
    */
    $renderer = app(\Siteman\Cms\Blocks\BlockRenderer::class)
@endphp
<header>
    <ul>
        @foreach(\Siteman\Cms\Facades\Siteman::getMenuItems('header') as $item)
            <li><a href="{{$item->url}}" class="hover:underline">>{{$item->title}}</a></li>
        @endforeach
    </ul>
</header>
<main>
@foreach($post->blocks ?? [] as $block)
    {!! $renderer->render($block, $post)  !!}
@endforeach
</main>
<footer>
    <ul>
        @foreach(\Siteman\Cms\Facades\Siteman::getMenuItems('footer') as $item)
            <li><a href="{{$item->url}}" class="hover:underline">>{{$item->title}}</a></li>
        @endforeach
    </ul>
</footer>
