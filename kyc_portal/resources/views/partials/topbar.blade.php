@php
    $user = auth()->user();
@endphp

<header class="portal-topbar">
    <div class="topbar-left">
        <button
            type="button"
            class="mobile-menu"
            data-sidebar-toggle
            aria-controls="portal-sidebar"
            aria-expanded="false"
            aria-label="Open navigation"
        >
            <x-icon name="menu" />
        </button>
        <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
    </div>

    @if ($user)
        <div class="dropdown">
            <button
                class="user-menu"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <span class="avatar" aria-hidden="true">{{ $user->initials() }}</span>
                <span class="user-copy">
                    <span class="user-name">{{ $user->name }}</span>
                    <span class="user-role">{{ $user->primaryRoleLabel() }}</span>
                </span>
                <x-icon name="chevron-down" :size="15" />
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">My profile</a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Sign out</button>
                    </form>
                </li>
            </ul>
        </div>
    @endif
</header>
