<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}"/>
        <title>@yield('title')</title>
    </head>
    <body>
        @yield('content')
    </body>
    <script src="{{ asset('js/app.js') }}"></script>
</html>
