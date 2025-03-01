@extends('siteman::themes.blank.wrapper')
@php
    /**
    * @var \Siteman\Cms\Models\Page $tagIndexPage
    * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $tags
    */
@endphp

@section('content')

    <ul>
        @foreach($tags as $tag)
            @php
                /**
                * @var \Siteman\Cms\Models\Tag $tag
                */
            @endphp
            <li>
                <a href="{{$tagIndexPage->computed_slug.'/'.$tag->slug}}">{{$tag->slug}} ({{$tag->pages_count}})</a>
            </li>
        @endforeach
    </ul>
    {{$tags->links('pagination::simple-default')}}

@endsection
