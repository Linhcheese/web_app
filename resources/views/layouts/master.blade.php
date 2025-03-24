<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>@yield('title', 'Trang Web')</title>
    <link rel="stylesheet" href="{{asset('style/master.css')}}">
    @yield('styles')
    @yield('scriptTops')
    @vite('resources/css/app.css')
</head>
<body>
    <div class="main-container {{$extendClass ?? ''}}">
        @yield('content')
    </div>
    @include('layouts.footer')
</body>
</html>
