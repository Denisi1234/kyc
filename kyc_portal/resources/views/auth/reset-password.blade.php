@extends('layouts.auth')

@section('title', 'Choose a new password')

@section('content')
    <h1>Choose a new password</h1>
    <p class="auth-subtitle">Use at least 12 characters with upper and lower case letters, a number and a symbol.</p>

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $email) }}"
                class="form-control @error('email') is-invalid @enderror"
                autocomplete="email"
                required
            >
            @error('email')
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

        <button type="submit" class="btn btn-primary w-100">Reset password</button>
    </form>

    <div class="auth-links">
        <a href="{{ route('login') }}">Back to sign in</a>
    </div>
@endsection
