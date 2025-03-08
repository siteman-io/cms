@props(['page'])
{!! app(Siteman::getPageTypes()[$page->type])->render(request(), $page) !!}
