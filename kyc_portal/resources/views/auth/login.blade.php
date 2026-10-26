@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <h1>Sign in</h1>
    <p class="auth-subtitle">Use your email address or phone number to access the choir portal.</p>

    <form method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="identifier" class="form-label">Email or phone number</label>
            <input
                id="identifier"
                name="identifier"
                type="text"
                value="{{ old('identifier') }}"
                class="form-control @error('identifier') is-invalid @enderror"
                autocomplete="username"
                autofocus
                required
            >
            @error('identifier')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <label for="password" class="form-label">Password</label>
                <a href="{{ route('password.request') }}" class="text-muted-sm">Forgot password?</a>
            </div>
            <input
                id="password"
                name="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                autocomplete="current-password"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))>
            <label class="form-check-label" for="remember">Keep me signed in on this device</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Sign in</button>
    </form>

    <p class="text-muted-sm mt-3 mb-0">
        Accounts are issued after choir membership is approved. If you have applied and cannot sign in, contact the choir office.
    </p>
@endsection
