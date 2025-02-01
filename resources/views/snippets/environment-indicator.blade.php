<div
    class="
        hidden sm:flex items-center h-9 px-3 text-sm font-medium
        rounded-lg shadow-sm ring-1
        ring-custom-600/20 bg-custom-50 text-custom-600
        dark:ring-custom-400/30 dark:bg-custom-400/10 dark:text-custom-400
    "
    style="
        --c-50: {{ $color[50] }};
        --c-300: {{ $color[300] }};
        --c-400: {{ $color[400] }};
        --c-600: {{ $color[600] }};
    "
>
    {{ $environment }}

</div>
<style>
    .fi-topbar,
    .fi-sidebar {
        border-top: 5px solid rgb({{$color['500']}}) !important;
    }

    .fi-topbar {
        height: calc(4rem + 5px) !important;
    }
</style>
