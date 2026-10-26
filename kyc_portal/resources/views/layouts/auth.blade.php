<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Sign in') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="auth-page">
        <div class="auth-wrap">
            <a href="{{ url('/') }}" class="auth-brand">
                <span class="brand-mark" aria-hidden="true">KYC</span>
                <span class="auth-brand-copy">
                    <strong>Kijitonyama Youth Choir</strong>
                    <span>Management portal</span>
                </span>
            </a>

            <div class="auth-card">
                @include('partials.flash')
                @yield('content')
            </div>

            <p class="auth-footer">
                Access is limited to approved choir members and leaders.<br>
                &copy; {{ date('Y') }} Kijitonyama Youth Choir
            </p>
        </div>
    </main>
</body>
</html>
