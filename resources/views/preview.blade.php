@props(['page'])
{!! app(\Siteman\Cms\Facades\Siteman::getPageTypes()[$page->type])->render(request(), $page) !!}
