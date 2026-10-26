@extends('layouts.portal')

@section('title', 'Edit user')
@section('page-title', 'Edit user')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Edit user</h1>
            <p>{{ $user->name }} · {{ $user->email ?? $user->phone }}</p>
        </div>
        <div class="heading-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back to users</a>
        </div>
    </div>

    <div class="grid-2">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Account settings</h2>
                    <p class="panel-subtitle">Role changes and status updates are audited.</p>
                </div>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" novalidate>
                    @csrf
                    @method('PATCH')
                    @include('admin.users._form', [
                        'submitLabel' => 'Save changes',
                    ])
                </form>
            </div>
        </section>

        <div>
            <section class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Account summary</h2>
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
                        <dt>Last sign-in</dt>
                        <dd>{{ $user->last_login_at?->format('d M Y, H:i') ?? 'Never' }}</dd>
                        <dt>Created</dt>
                        <dd>{{ $user->created_at?->format('d M Y, H:i') }}</dd>
                        <dt>Last updated</dt>
                        <dd>{{ $user->updated_at?->format('d M Y, H:i') }}</dd>
                    </dl>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">Account access</h2>
                        <p class="panel-subtitle">Deactivation blocks sign-in immediately.</p>
                    </div>
                </div>
                <div class="panel-body">
                    @if ($user->is(auth()->user()))
                        <p class="text-muted-sm mb-0">You cannot deactivate your own account. Ask another administrator to make this change.</p>
                    @elseif ($user->is_active)
                        <p class="text-muted-sm">The user will keep their records but will no longer be able to sign in.</p>
                        <form
                            method="POST"
                            action="{{ route('admin.users.deactivate', $user) }}"
                            data-confirm="Deactivate {{ $user->name }}? They will not be able to sign in."
                        >
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">Deactivate account</button>
                        </form>
                    @else
                        <p class="text-muted-sm">The account is inactive. Reactivate it to restore sign-in.</p>
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">Activate account</button>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
