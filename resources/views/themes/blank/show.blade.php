@extends('siteman::themes.blank.wrapper')
@php
    /**
    * @var \Siteman\Cms\Models\BasePostType $post
    */
@endphp

@section('seo')
    {!! seo()->for($post) !!}
@endsection

@section('content')
    <x-dynamic-component component="{{ $post->layout ?? 'base-layout' }}" :post="$post" />
@endsection
