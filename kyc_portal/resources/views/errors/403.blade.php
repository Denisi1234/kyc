@extends('layouts.auth')

@section('title', 'Access denied')

@section('content')
    <h1>Access denied</h1>
    <p class="auth-subtitle">Your account does not have permission to view this page.</p>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to dashboard</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Sign out</button>
        </form>
    </div>

    <p class="text-muted-sm mt-3 mb-0">
        If you believe this is a mistake, contact a System Admin and ask them to review your role.
    </p>
@endsection
