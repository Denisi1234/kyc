@php
    $site = config('public_site');
    $contact = $site['contact'];
    $navigation = $site['navigation'];
    $currentPage = request()->route('page');
    $isHome = request()->routeIs('public.home');
    $isActiveNav = function (array $item) use ($currentPage) {
        if (isset($item['route'])) {
            return request()->routeIs($item['route']);
        }

        if (isset($item['routes'])) {
            return request()->routeIs($item['routes']);
        }

        if (isset($item['pages'])) {
            return in_array($currentPage, $item['pages'], true);
        }

        if (isset($item['page'])) {
            return $currentPage === $item['page'];
        }

        return in_array($currentPage, array_column($item['children'] ?? [], 'page'), true);
    };
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kijitonyama Youth Choir · Sing with us, carry the song')</title>
    <meta name="description" content="@yield('description', 'Kijitonyama Youth Choir is a community of young singers in Dar es Salaam, Tanzania, serving the church and community through sacred choral music.')">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Kijitonyama Youth Choir">
    <meta property="og:title" content="@yield('title', 'Kijitonyama Youth Choir')">
    <meta property="og:description" content="@yield('description', 'Kijitonyama Youth Choir is a community of young singers in Dar es Salaam, Tanzania, serving the church and community through sacred choral music.')">
    <meta property="og:image" content="{{ asset('assets/img/team-banner.jpg') }}">
    <link rel="icon" href="{{ asset('assets/img/favicon-32.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/favicon-180.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Marcellus&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/public.css', 'resources/js/public.js'])
</head>
<body class="pc-body">
    @include('partials.public-icons')

    <a class="pc-skip" href="#main-content">Skip to content</a>

    <div class="pc-utility">
        <div class="pc-container pc-utility__inner">
            <div class="pc-utility__actions">
                <a class="pc-utility__icon" href="{{ route('public.page', 'contact') }}" aria-label="Search">
                    <svg aria-hidden="true"><use href="#pc-i-search"></use></svg>
                </a>
                <a class="pc-utility__icon" href="{{ route('login') }}" aria-label="Member login">
                    <svg aria-hidden="true"><use href="#pc-i-user"></use></svg>
                </a>
                <a class="pc-utility__btn pc-utility__btn--tickets" href="{{ route('public.page', 'events') }}">TICKETS</a>
            </div>
        </div>
    </div>

    <header class="pc-header">
        <div class="pc-container pc-header__inner">
            <a class="pc-brand" href="{{ route('public.home') }}" aria-label="Kijitonyama Youth Choir home">
                <img src="{{ asset('assets/img/kyc-logo-160.jpg') }}" alt="" width="66" height="66">
                <span class="pc-brand__wordmark">
                    <span>Kijitonyama</span>
                    <span>Youth Choir</span>
                </span>
            </a>

            <nav class="pc-primary-nav" aria-label="Primary">
                <ul class="pc-nav">
                    @foreach ($navigation as $item)
                        @php
                            $children = $item['children'] ?? [];
                            $active = $isActiveNav($item);
                        @endphp
                        <li class="pc-nav__item {{ $active ? 'is-active' : '' }}">
                            @if (isset($item['route']))
                                <a class="pc-nav__link" href="{{ route($item['route']) }}" @if ($active) aria-current="page" @endif>{{ $item['label'] }}</a>
                            @elseif (isset($item['page']))
                                <a class="pc-nav__link" href="{{ route('public.page', $item['page']) }}" @if ($active) aria-current="page" @endif>{{ $item['label'] }}</a>
                            @else
                                <a class="pc-nav__link" href="{{ route('public.page', $children[0]['page']) }}">
                                    {{ $item['label'] }}
                                    <svg aria-hidden="true"><use href="#pc-i-chevron-down"></use></svg>
                                </a>
                                <ul class="pc-nav__menu">
                                    @foreach ($children as $child)
                                        <li><a href="{{ route('public.page', $child['page']) }}">{{ $child['label'] }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>

            <button class="pc-nav-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="pc-drawer" data-pc-menu-open>
                <span class="pc-nav-toggle__box" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>
        </div>
    </header>

    <div class="pc-drawer" id="pc-drawer" hidden aria-hidden="true">
        <div class="pc-drawer__backdrop" data-pc-menu-close></div>
        <aside class="pc-drawer__panel" role="dialog" aria-modal="true" aria-label="Site menu">
            <div class="pc-drawer__head">
                <a class="pc-drawer__brand" href="{{ route('public.home') }}">
                    <img src="{{ asset('assets/img/kyc-logo-160.jpg') }}" alt="" width="46" height="46">
                    <span class="pc-drawer__brand-text">
                        <span class="pc-drawer__brand-name">Kijitonyama</span>
                        <span class="pc-drawer__brand-sub">Youth Choir &middot; Dar es Salaam</span>
                    </span>
                </a>
                <button class="pc-drawer__close" type="button" aria-label="Close menu" data-pc-menu-close>
                    <svg aria-hidden="true"><use href="#pc-i-close"></use></svg>
                </button>
            </div>

            <div class="pc-drawer__body">
                <p class="pc-drawer__label">Menu</p>
                <nav class="pc-drawer__nav" aria-label="Mobile navigation">
                    @foreach ($navigation as $item)
                        @php
                            $children = $item['children'] ?? [];
                            $active = $isActiveNav($item);
                        @endphp
                        @if (! empty($children))
                            <details class="pc-drawer__group">
                                <summary class="pc-drawer__link {{ $active ? 'is-active' : '' }}">
                                    <span>{{ $item['label'] }}</span>
                                    <svg aria-hidden="true"><use href="#pc-i-chevron-down"></use></svg>
                                </summary>
                                <div class="pc-drawer__sublinks">
                                    @foreach ($children as $child)
                                        <a class="pc-drawer__sublink" href="{{ route('public.page', $child['page']) }}">{{ $child['label'] }}</a>
                                    @endforeach
                                </div>
                            </details>
                        @else
                            <a class="pc-drawer__link {{ $active ? 'is-active' : '' }}"
                               href="{{ isset($item['route']) ? route($item['route']) : route('public.page', $item['page']) }}"
                               @if ($active) aria-current="page" @endif>
                                <span>{{ $item['label'] }}</span>
                                <svg aria-hidden="true"><use href="#pc-i-arrow-right"></use></svg>
                            </a>
                        @endif
                    @endforeach
                </nav>

                <div class="pc-drawer__cta">
                    <a class="pc-drawer__cta-btn pc-drawer__cta-btn--primary" href="{{ route('public.page', 'join') }}">
                        Join the Choir
                        <svg aria-hidden="true"><use href="#pc-i-arrow-right"></use></svg>
                    </a>
                    <a class="pc-drawer__cta-btn" href="{{ route('login') }}">
                        <svg aria-hidden="true"><use href="#pc-i-user"></use></svg>
                        Member Login
                    </a>
                </div>

                <div class="pc-drawer__meta">
                    <p class="pc-drawer__label">Get in touch</p>
                    <a class="pc-drawer__meta-link" href="tel:{{ $contact['phone_link'] }}">
                        <svg aria-hidden="true"><use href="#pc-i-phone"></use></svg>
                        <span>{{ $contact['phone'] }}</span>
                    </a>
                    <a class="pc-drawer__meta-link" href="mailto:{{ $contact['email'] }}">
                        <svg aria-hidden="true"><use href="#pc-i-mail"></use></svg>
                        <span>{{ $contact['email'] }}</span>
                    </a>
                    <span class="pc-drawer__meta-link">
                        <svg aria-hidden="true"><use href="#pc-i-map-pin"></use></svg>
                        <span>{{ $contact['address'] }}</span>
                    </span>
                    <div class="pc-drawer__socials">
                        <a class="pc-drawer__social" href="{{ $contact['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram">
                            <svg aria-hidden="true"><use href="#pc-i-instagram"></use></svg>
                        </a>
                        <a class="pc-drawer__social" href="{{ $contact['youtube'] }}" target="_blank" rel="noopener" aria-label="YouTube">
                            <svg aria-hidden="true"><use href="#pc-i-youtube"></use></svg>
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="pc-footer-clone">
        <div class="pc-container">
            <div class="pc-footer-clone__grid">
                <div class="pc-footer-clone__col pc-footer-clone__col--brand">
                    <a href="{{ route('public.home') }}" class="pc-footer-clone__logo">
                        <img src="{{ asset('assets/img/kyc-logo-160.jpg') }}" alt="Kijitonyama Youth Choir" width="54" height="54">
                        <span class="pc-footer-clone__wordmark">
                            <span>Kijitonyama</span>
                            <span>Youth Choir</span>
                        </span>
                    </a>

                    <address class="pc-footer-clone__address">
                        {{ $contact['church'] }}<br>
                        {{ $contact['address'] }}<br>
                        <strong>PHONE:</strong> <a href="tel:{{ $contact['phone_link'] }}">{{ $contact['phone'] }}</a><br>
                        <strong>EMAIL:</strong> <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>
                    </address>
                </div>

                <div class="pc-footer-clone__col">
                    <h4 class="pc-footer-clone__heading">EXPLORE</h4>
                    <ul class="pc-footer-clone__list">
                        @foreach ($navigation as $item)
                            @php
                                $href = isset($item['route'])
                                    ? route($item['route'])
                                    : route('public.page', $item['page']);
                            @endphp
                            <li><a href="{{ $href }}">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="pc-footer-clone__col">
                    <h4 class="pc-footer-clone__heading">MORE</h4>
                    <ul class="pc-footer-clone__list">
                        <li><a href="{{ route('public.page', 'join') }}">Join the Choir</a></li>
                        <li><a href="{{ route('public.page', 'history') }}">Our History</a></li>
                        <li><a href="{{ route('public.page', 'mission') }}">Mission &amp; Vision</a></li>
                        <li><a href="{{ route('public.page', 'leadership') }}">Leadership</a></li>
                    </ul>
                </div>

                <div class="pc-footer-clone__col">
                    <h4 class="pc-footer-clone__heading">FOLLOW US</h4>

                    <div class="pc-footer-clone__socials">
                        <a href="{{ $contact['youtube'] }}" target="_blank" rel="noopener" class="pc-social-circle pc-social-circle--youtube" aria-label="YouTube">
                            <svg aria-hidden="true"><use href="#pc-i-youtube"></use></svg>
                        </a>
                        <a href="{{ $contact['instagram'] }}" target="_blank" rel="noopener" class="pc-social-circle pc-social-circle--instagram" aria-label="Instagram">
                            <svg aria-hidden="true"><use href="#pc-i-instagram"></use></svg>
                        </a>
                    </div>

                    <div class="pc-footer-clone__newsletter">
                        <h4 class="pc-footer-clone__heading">NEWSLETTER SIGNUP</h4>
                        <form class="pc-footer-newsletter-form" data-pc-newsletter novalidate>
                            <input type="email" placeholder="Enter Your Email Address" required autocomplete="email">
                            <button type="submit">SUBMIT</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="pc-footer-clone__bottom">
                <div class="pc-footer-clone__copy">
                    &copy; {{ date('Y') }} Kijitonyama Youth Choir
                    <span class="pc-divider-dot">|</span>
                    <a href="{{ route('public.page', 'privacy') }}">Privacy Policy</a>
                    <span class="pc-divider-dot">|</span>
                    <a href="{{ route('login') }}">Member Portal</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
