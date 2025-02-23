@extends('siteman::themes.blank.wrapper')
@php
    /**
    * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $posts
    */
@endphp

@section('content')

    <ul>
        @foreach($posts as $post)
            @php
                /**
                * @var \Siteman\Cms\Models\Post $post
                */
            @endphp
            <li>
                <a href="{{$post->path()}}">{!! $post->title !!}</a>
            </li>
        @endforeach
    </ul>
    {{$posts->links('pagination::simple-default')}}

@endsection
