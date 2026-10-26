@extends('layouts.portal')

@section('title', 'Roles & permissions')
@section('page-title', 'Roles & permissions')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Roles &amp; permissions</h1>
            <p>Roles are fixed to the choir structure. Adjust what each role can do; every change is recorded in the audit trail.</p>
        </div>
    </div>

    @foreach ($roles as $role)
        @php
            $roleName = \App\Enums\RoleName::tryFrom($role->name);
            $label = $roleName?->label() ?? \Illuminate\Support\Str::headline($role->name);
            $isSystemRole = $role->name === \App\Enums\RoleName::SystemAdmin->value;
            $rolePermissions = $role->permissions->pluck('name')->all();
        @endphp

        <section class="panel role-card">
            <div class="panel-header">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h2 class="panel-title mb-0">{{ $label }}</h2>
                        @if ($isSystemRole)
                            <span class="badge badge-system">System role</span>
                        @endif
                        <span class="badge badge-count">{{ $role->users_count }} {{ \Illuminate\Support\Str::plural('user', $role->users_count) }}</span>
                    </div>
                    <p class="panel-subtitle">
                        {{ $roleName?->description() ?? 'Choir role.' }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                @csrf
                @method('PUT')

                <div class="panel-body">
                    @if ($isSystemRole)
                        <div class="alert alert-info py-2">
                            The System Admin role is fixed. Its permissions cannot be reduced from this screen.
                        </div>
                    @endif

                    <div class="permission-grid">
                        @foreach ($groups as $groupName => $permissions)
                            <div class="permission-group">
                                <h3>{{ $groupName }}</h3>
                                @foreach ($permissions as $permission)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="permissions[]"
                                            id="permission-{{ $role->id }}-{{ str_replace('.', '-', $permission) }}"
                                            value="{{ $permission }}"
                                            @checked(in_array($permission, $rolePermissions, true))
                                            @disabled($isSystemRole)
                                        >
                                        <label class="form-check-label" for="permission-{{ $role->id }}-{{ str_replace('.', '-', $permission) }}">
                                            {{ \App\Support\PermissionCatalog::label($permission) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                @unless ($isSystemRole)
                    @can('roles.manage')
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary">Save permissions</button>
                        </div>
                    @endcan
                @endunless
            </form>
        </section>
    @endforeach
@endsection
