@if($showToc)
<div class="grid grid-cols-4 gap-3">
    <div class="col-span-3">
        {!! $content !!}
    </div>
    <div class="col-span-1">
        {!! $toc !!}
    </div>
</div>
@else
{!! $content !!}
@endif

