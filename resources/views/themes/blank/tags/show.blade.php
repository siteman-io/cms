@extends('siteman::themes.blank.wrapper')
@php
    /**
    * @var \Siteman\Cms\Models\Tag $tag
    * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \Siteman\Cms\Models\Page> $pages
    */
@endphp

@section('content')

    <ul>
        @foreach($pages as $page)
            <li>
                <a href="{{$page->computed_slug}}">{!! $page->title !!}</a>
            </li>
        @endforeach
    </ul>
    {{$pages->links('pagination::simple-default')}}

@endsection
