@extends('siteman::themes.blank.wrapper')
@php
    /**
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
                <a href="{{$tag->url()}}">{{$tag->slug}} ({{$tag->posts_count}})</a>
            </li>
        @endforeach
    </ul>
    {{$tags->links('pagination::simple-default')}}

@endsection
