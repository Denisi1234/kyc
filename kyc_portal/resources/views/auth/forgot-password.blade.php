@extends('layouts.auth')

@section('title', 'Reset password')

@section('content')
    <h1>Reset your password</h1>
    <p class="auth-subtitle">Enter the email address on your account. We will send a password reset link.</p>

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                autocomplete="email"
                autofocus
                required
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Send reset link</button>
    </form>

    <div class="auth-links">
        <a href="{{ route('login') }}">Back to sign in</a>
    </div>

    <p class="text-muted-sm mt-3 mb-0">
        Accounts without an email address cannot use self-service reset. Contact an administrator for help.
    </p>
@endsection
