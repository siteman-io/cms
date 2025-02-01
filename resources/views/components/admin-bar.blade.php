@php
    $currentUser =auth()->user();
    $currentPage = \Illuminate\Support\Facades\Context::get('current_page');
    $currentPost = \Illuminate\Support\Facades\Context::get('current_post');
    $panel = \Filament\Facades\Filament::getDefaultPanel();
@endphp
@if($currentUser instanceof \Filament\Models\Contracts\FilamentUser && $currentUser->canAccessPanel($panel))
    <link href="{{asset('css/siteman/admin-bar.css')}}" rel="stylesheet">
    <style>
        body {
            padding-top: 34px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const adminBar = document.getElementById('adminBar');
            const isHidden = localStorage.getItem('adminBarHidden') === 'true';

            if (isHidden) {
                adminBar.style.display = 'none';
            }

            document.getElementById('admin-bar-hide-btn').addEventListener('click', function () {
                adminBar.style.display = 'none';
                localStorage.setItem('adminBarHidden', 'true');
            });
            document.getElementById('admin-bar-show-btn').addEventListener('click', function () {
                adminBar.style.display = 'flex';
                localStorage.setItem('adminBarHidden', 'false');
            });
        });
    </script>

    <div class="flex flex-row admin-bar z-10 font-sans text-white bg-gray-800 pr-4 fixed right-0 top-0 left-0 shadow-lg"
         id="adminBar">
        <div class="font-bold py-1 px-2 mr-2">{{config('app.name')}}</div>
        <a class="flex items-center space-x-2 pr-2 mr-2 text-white no-underline hover:underline"
           href="{{$panel->getUrl()}}">
            @svg('heroicon-o-wrench-screwdriver', 'h-4 w-4')
            <span>{{ __('siteman::general.back-to-dashboard') }}</span>
        </a>

        @if($currentPage && $currentUser->can('update_page', $currentPage))
            <a class="flex items-center space-x-2 text-white no-underline hover:underline"
               href="{{$panel->getResourceUrl($currentPage, 'edit')}}">
                @svg('heroicon-o-paint-brush', 'h-4 w-4')
                <span>{{ __('siteman::general.edit-page') }}</span>
            </a>
        @endif
        @if($currentPost && $currentUser->can('update_post', $currentPost))
            <a class="flex items-center space-x-2 text-white no-underline hover:underline"
               href="{{$panel->getResourceUrl($currentPost, 'edit')}}">
                @svg('heroicon-o-paint-brush', 'h-4 w-4')
                <span>{{ __('siteman::general.edit-post') }}</span>
            </a>
        @endif
        <button id="admin-bar-hide-btn" class="ml-auto cursor-pointer border-0 bg-transparent text-white">
            @svg('heroicon-o-arrow-up', 'h-4 w-4')
        </button>
    </div>
    <button id="admin-bar-show-btn"
            class="fixed top-0 right-0 cursor-pointer border-0 bg-transparent text-black mr-4 mt-1">
        @svg('heroicon-o-arrow-down', 'h-4 w-4')
    </button>
@endif
