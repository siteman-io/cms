<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    @yield('seo')
</head>
<body>
<header>
    <ul class="flex gap-4">
        @foreach(\Siteman\Cms\Facades\Siteman::getMenuItems('header') as $item)
            <li class="relative group">
                <a href="{{ $item->url }}" class="hover:underline">{{ $item->title }}</a>
                @if($item->children->isNotEmpty())
                    <ul class="absolute left-0 top-full hidden group-hover:block bg-white shadow-lg p-2 min-w-60">
                        @foreach($item->children as $child)
                            <li>
                                <a href="{{ $child->url }}" class="block px-2 py-1 hover:bg-gray-100">{{ $child->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</header>
@yield('content')
<footer>
    <ul class="flex gap-4">
        @foreach(\Siteman\Cms\Facades\Siteman::getMenuItems('footer') as $item)
            <li>
                <a href="{{ $item->url }}" class="hover:underline">{{ $item->title }}</a>
                @if($item->children->isNotEmpty())
                    <ul class="ml-4 mt-1">
                        @foreach($item->children as $child)
                            <li>
                                <a href="{{ $child->url }}" class="text-sm hover:underline">{{ $child->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</footer>
</body>
</html>
