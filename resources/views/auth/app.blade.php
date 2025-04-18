<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>@yield('title', 'Trang Web')</title>
    <link rel="stylesheet" href="{{asset('style/auth/app.css')}}">
    @yield('styles')
    @vite('resources/css/app.css')
</head>
<body>
    <div class="main-container">
        @yield('content')
    </div>
</body>
</html>
