<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-body">
    <a class="skip-link" href="#main-content">Skip to content</a>

    <div class="portal-shell">
        @include('partials.sidebar')

        <div class="portal-main">
            @include('partials.topbar')

            <main id="main-content" class="portal-content">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>

    <div class="sidebar-backdrop" data-sidebar-close aria-hidden="true"></div>

    @stack('scripts')
</body>
</html>
