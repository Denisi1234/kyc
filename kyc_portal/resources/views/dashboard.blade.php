@extends('layouts.portal')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Dashboard</h1>
            <p>A summary of your access and the current state of {{ $organizationName }} portal administration.</p>
        </div>
    </div>

    <div class="grid-2">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Your account</h2>
                    <p class="panel-subtitle">Signed in as {{ $user->name }}</p>
                </div>
                <span class="avatar" aria-hidden="true">{{ $user->initials() }}</span>
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
                            @foreach ($user->roleLabels() as $role)
                                <span class="badge badge-role">{{ $role }}</span>
                            @endforeach
                        </div>
                    </dd>
                    <dt>Last sign-in</dt>
                    <dd>{{ $user->last_login_at?->format('d M Y, H:i') ?? 'This is your first sign-in' }}</dd>
                    <dt>Contact</dt>
                    <dd>
                        {{ $user->email ?? 'No email' }}
                        <div class="text-muted-sm">{{ $user->phone ?? 'No phone number' }}</div>
                    </dd>
                </dl>

                <div class="divider"></div>
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm">Update profile</a>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Your access</h2>
                    <p class="panel-subtitle">Permissions granted by your assigned roles.</p>
                </div>
            </div>
            <div class="panel-body">
                @if ($capabilities)
                    <ul class="capability-list">
                        @foreach ($capabilities as $capability)
                            <li>{{ $capability }}</li>
                        @endforeach
                    </ul>
                    <p class="text-muted-sm mt-3 mb-0">
                        Administrative permissions are separated from choir and finance duties. Contact a System Admin if something is missing.
                    </p>
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><x-icon name="shield" /></div>
                        <h3>No permissions yet</h3>
                        <p>Ask a System Admin to assign your role.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    @can('users.view')
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Administration</h2>
                    <p class="panel-subtitle">Live portal state, not sample data.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Manage users</a>
            </div>
            <div class="panel-body">
                <div class="grid-3">
                    <div class="stat">
                        <div class="stat-label">Active users</div>
                        <div class="stat-value">{{ number_format($activeUserCount) }}</div>
                        <div class="stat-note">Accounts that can sign in</div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">Active administrators</div>
                        <div class="stat-value">{{ number_format($adminCount) }}</div>
                        <div class="stat-note">System Admin accounts</div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">Roles</div>
                        <div class="stat-value">{{ number_format($roleCount) }}</div>
                        <div class="stat-note">Fixed choir roles</div>
                    </div>
                </div>

                <div class="divider"></div>

                <ul class="checklist">
                    <li>
                        <x-icon name="{{ $hasMinimumAdmins ? 'check' : 'alert' }}" class="{{ $hasMinimumAdmins ? 'ok' : 'warn' }}" />
                        <div>
                            <strong>{{ $hasMinimumAdmins ? 'Two active administrators' : 'Add a second administrator' }}</strong>
                            <span>
                                @if ($hasMinimumAdmins)
                                    Last-admin protection is active.
                                @else
                                    Create a second System Admin so access does not depend on one person.
                                @endif
                            </span>
                        </div>
                    </li>
                    <li>
                        <x-icon name="check" class="ok" />
                        <div>
                            <strong>Organisation settings</strong>
                            <span>{{ $organizationName }}</span>
                        </div>
                    </li>
                    <li>
                        <x-icon name="check" class="ok" />
                        <div>
                            <strong>Audit trail</strong>
                            <span>Authentication and administration changes are recorded.</span>
                        </div>
                    </li>
                </ul>
            </div>
        </section>
    @endcan

    @can('audit.view')
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Recent activity</h2>
                    <p class="panel-subtitle">Latest audited events across the portal.</p>
                </div>
                <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-secondary btn-sm">View audit trail</a>
            </div>
            @if ($recentAudit->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon"><x-icon name="activity" /></div>
                    <h3>No activity recorded</h3>
                    <p>Events will appear here as users sign in and administrators make changes.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">When</th>
                                <th scope="col">Event</th>
                                <th scope="col">Actor</th>
                                <th scope="col">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAudit as $log)
                                <tr>
                                    <td class="nowrap text-muted-sm">{{ $log->created_at?->format('d M, H:i') }}</td>
                                    <td><span class="badge badge-neutral">{{ \App\Enums\AuditEvent::tryFrom($log->event)?->label() ?? $log->event }}</span></td>
                                    <td>{{ $log->actor_name ?? 'System' }}</td>
                                    <td>{{ $log->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endcan

    @cannot('users.view')
        <section class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Need help?</h2>
            </div>
            <div class="panel-body">
                <p class="mb-2">For changes to your role, contact details or contributions record, contact the choir office.</p>
                @if ($organizationPhone)
                    <p class="mb-0 d-flex align-items-center gap-2">
                        <x-icon name="phone" :size="16" />
                        <a href="tel:{{ preg_replace('/\s+/', '', $organizationPhone) }}">{{ $organizationPhone }}</a>
                    </p>
                @endif
            </div>
        </section>
    @endcannot
@endsection
