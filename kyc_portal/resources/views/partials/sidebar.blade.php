@php
    $user = auth()->user();
@endphp

<aside class="portal-sidebar" id="portal-sidebar" aria-label="Portal navigation">
    <a href="{{ route('dashboard') }}" class="brand">
        <span class="brand-mark" aria-hidden="true">KYC</span>
        <span class="brand-copy">
            <span class="brand-name">KYC Choir</span>
            <span class="brand-subtitle">Management portal</span>
        </span>
    </a>

    <nav class="sidebar-nav">
        <div class="nav-section">Overview</div>
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <x-icon name="dashboard" />
            <span>Dashboard</span>
        </a>

        <div class="nav-section">Account</div>
        <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <x-icon name="user" />
            <span>My profile</span>
        </a>

        @can('events.manage')
            <div class="nav-section">Content</div>
            <a href="{{ route('admin.events.index') }}" class="sidebar-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                <x-icon name="calendar" />
                <span>Events</span>
            </a>
        @endcan

        @canany(['users.view', 'roles.view', 'audit.view', 'settings.view'])
            <div class="nav-section">Administration</div>

            @can('users.view')
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <x-icon name="users" />
                    <span>Users</span>
                </a>
            @endcan

            @can('roles.view')
                <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <x-icon name="shield" />
                    <span>Roles &amp; permissions</span>
                </a>
            @endcan

            @can('audit.view')
                <a href="{{ route('admin.audit.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
                    <x-icon name="activity" />
                    <span>Audit trail</span>
                </a>
            @endcan

            @can('settings.view')
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <x-icon name="settings" />
                    <span>Settings</span>
                </a>
            @endcan
        @endcanany
    </nav>

    <div class="sidebar-footer">
        {{ $user?->primaryRoleLabel() ?? 'Signed in' }}<br>
        {{ config('app.name') }}
    </div>
</aside>
