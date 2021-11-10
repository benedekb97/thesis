<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}"/>
        <title>@yield('title')</title>
    </head>
    <body>
        @include('layouts.partial.navbar')
        <main>
            <div class="container container-main">
                @yield('content')
            </div>
        </main>
        @stack('modals')
    </body>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</html>
