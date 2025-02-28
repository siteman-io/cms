@extends('siteman::themes.blank.wrapper')
@php
    /**
    * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $posts
    */
@endphp

@section('content')
eqqe
    <ul>
        @foreach($posts as $post)
            @php
                /**
                * @var \Siteman\Cms\Models\Page $post
                */
            @endphp
            <li>
                <a href="{{$post->computed_slug}}">{!! $post->title !!}</a>
            </li>
        @endforeach
    </ul>
    {{$posts->links('pagination::simple-default')}}

@endsection
