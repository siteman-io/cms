@extends('siteman::themes.blank.wrapper')
@php
    /**
    * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $collection
    */
@endphp

@section('content')

    <ul>
        @foreach($collection as $post)
            <li>
                <a href="{{$post->path()}}">{!! $post->title !!}</a>
            </li>
        @endforeach
    </ul>
    {{$collection->links()}}

@endsection
