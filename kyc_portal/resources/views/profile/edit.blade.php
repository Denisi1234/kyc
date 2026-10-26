@extends('layouts.portal')

@section('title', 'My profile')
@section('page-title', 'My profile')

@section('content')
    <div class="page-heading">
        <div>
            <h1>My profile</h1>
            <p>Keep your contact details current. Your role permissions are managed by an administrator.</p>
        </div>
    </div>

    @if ($user->must_change_password)
        <div class="alert alert-warning d-flex align-items-start gap-2" role="alert">
            <x-icon name="key" :size="17" />
            <div>
                <strong>Temporary password.</strong>
                Change your password below before using the rest of the portal.
            </div>
        </div>
    @endif

    <div class="grid-2">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Personal details</h2>
                    <p class="panel-subtitle">Used for portal sign-in and choir communication.</p>
                </div>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('profile.update') }}" novalidate>
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="name" class="form-label">Full name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}"
                            autocomplete="name"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            autocomplete="email"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Provide an email or a phone number. Email is required for self-service password reset.</div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone number</label>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $user->phone) }}"
                            autocomplete="tel"
                            placeholder="+255 753 423 251"
                        >
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Save details</button>
                </form>
            </div>
        </section>

        <div>
            <section class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Account</h2>
                </div>
                <div class="panel-body">
                    <dl class="detail-list">
                        <dt>Status</dt>
                        <dd>
                            @if ($user->is_active)
                                <span class="badge badge-active">Active</span>
                            @else
                                <span class="badge badge-inactive">Inactive</span>
                            @endif
                        </dd>

                        <dt>Roles</dt>
                        <dd>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse ($user->roleLabels() as $role)
                                    <span class="badge badge-role">{{ $role }}</span>
                                @empty
                                    <span class="text-muted-sm">No role assigned</span>
                                @endforelse
                            </div>
                        </dd>

                        <dt>Last sign-in</dt>
                        <dd>
                            {{ $user->last_login_at?->format('d M Y, H:i') ?? 'First sign-in pending' }}
                            @if ($user->last_login_ip)
                                <div class="text-muted-sm">{{ $user->last_login_ip }}</div>
                            @endif
                        </dd>

                        <dt>Member since</dt>
                        <dd>{{ $user->created_at?->format('d M Y') }}</dd>
                    </dl>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">Change password</h2>
                        <p class="panel-subtitle">Use at least 12 characters with mixed case, a number and a symbol.</p>
                    </div>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('profile.password.update') }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current password</label>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                autocomplete="current-password"
                                required
                            >
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="new-password"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm new password</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="form-control"
                                autocomplete="new-password"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary">Change password</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
