@props(['post'])
{!! app(Siteman::getPageTypes()[$post->type])->render(request(), $post) !!}
