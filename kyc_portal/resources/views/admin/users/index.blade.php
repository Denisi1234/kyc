@extends('layouts.portal')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Users</h1>
            <p>Portal accounts, roles and sign-in status. Users are created here; there is no public registration.</p>
        </div>
        @can('users.manage')
            <div class="heading-actions">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <x-icon name="plus" :size="16" class="me-1" />
                    Add user
                </a>
            </div>
        @endcan
    </div>

    <section class="panel">
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar">
                <div class="search-field">
                    <label for="search" class="form-label">Search</label>
                    <input
                        id="search"
                        name="search"
                        type="search"
                        class="form-control"
                        value="{{ $filters['search'] }}"
                        placeholder="Name, email or phone"
                    >
                </div>
                <div>
                    <label for="role" class="form-label">Role</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">All roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role['value'] }}" @selected($filters['role'] === $role['value'])>{{ $role['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">All statuses</option>
                        <option value="active" @selected($filters['status'] === 'active')>Active</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary">Filter</button>
                    @if ($filters['search'] !== '' || $filters['role'] !== '' || $filters['status'] !== '')
                        <a href="{{ route('admin.users.index') }}" class="btn btn-link px-1">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="panel">
        @if ($users->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><x-icon name="users" /></div>
                <h3>No users found</h3>
                <p>Adjust the filters, or add the first portal user.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Roles</th>
                            <th scope="col">Status</th>
                            <th scope="col">Last sign-in</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar" aria-hidden="true">{{ $user->initials() }}</span>
                                        <div>
                                            <div class="cell-title">{{ $user->name }}</div>
                                            <div class="cell-meta">Joined {{ $user->created_at?->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $user->email ?? 'No email' }}</div>
                                    <div class="cell-meta">{{ $user->phone ?? 'No phone' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse ($user->roleLabels() as $role)
                                            <span class="badge badge-role">{{ $role }}</span>
                                        @empty
                                            <span class="text-muted-sm">No role</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="badge badge-active">Active</span>
                                    @else
                                        <span class="badge badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-muted-sm nowrap">
                                    {{ $user->last_login_at?->format('d M Y, H:i') ?? 'Never' }}
                                </td>
                                <td class="text-end">
                                    @can('users.manage')
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @if ($user->is_active)
                                                @if (! $user->is(auth()->user()))
                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.users.deactivate', $user) }}"
                                                        data-confirm="Deactivate {{ $user->name }}? They will not be able to sign in."
                                                    >
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Deactivate</button>
                                                    </form>
                                                @endif
                                            @else
                                                <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Activate</button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted-sm">View only</span>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="panel-footer">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection
