<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>@yield('title')</title>
    </head>
    <body>
        @include('layouts.partial.navbar')
        <main style="padding-top: 20px;">
            <div class="container container-main">
                @yield('content')
            </div>
        </main>
        @stack('modals')
    </body>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</html>
