@php
    /**
    * @var string $layout
    * @var \Siteman\Cms\Models\Page $page
    */
@endphp

<x-dynamic-component :component="$layout" :page="$page" />
