@props(['post'])
{!! app(\Siteman\Cms\View\Renderer::class)->renderPostType($post) !!}
