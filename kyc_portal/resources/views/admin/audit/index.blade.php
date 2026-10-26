@extends('layouts.portal')

@section('title', 'Audit trail')
@section('page-title', 'Audit trail')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Audit trail</h1>
            <p>Append-only record of authentication, user, role and settings changes. Entries cannot be edited or deleted.</p>
        </div>
    </div>

    <section class="panel">
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.audit.index') }}" class="filter-bar">
                <div>
                    <label for="event" class="form-label">Event</label>
                    <select id="event" name="event" class="form-select">
                        <option value="">All events</option>
                        @foreach ($events as $value => $label)
                            <option value="{{ $value }}" @selected($filters['event'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="search-field">
                    <label for="actor" class="form-label">Actor</label>
                    <input
                        id="actor"
                        name="actor"
                        type="search"
                        class="form-control"
                        value="{{ $filters['actor'] }}"
                        placeholder="Name"
                    >
                </div>
                <div>
                    <label for="from" class="form-label">From</label>
                    <input id="from" name="from" type="date" class="form-control" value="{{ $filters['from'] }}">
                </div>
                <div>
                    <label for="to" class="form-label">To</label>
                    <input id="to" name="to" type="date" class="form-control" value="{{ $filters['to'] }}">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary">Filter</button>
                    @if (array_filter($filters))
                        <a href="{{ route('admin.audit.index') }}" class="btn btn-link px-1">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="panel">
        @if ($logs->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><x-icon name="activity" /></div>
                <h3>No audit events found</h3>
                <p>Events will appear here as people use the portal.</p>
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
                            <th scope="col">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="text-muted-sm nowrap">{{ $log->created_at?->format('d M Y, H:i:s') }}</td>
                                <td class="nowrap">
                                    <span class="badge badge-neutral">
                                        {{ \App\Enums\AuditEvent::tryFrom($log->event)?->label() ?? $log->event }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $log->actor_name ?? 'System' }}</div>
                                    @if ($log->subject_type)
                                        <div class="cell-meta">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</div>
                                    @endif
                                </td>
                                <td>
                                    {{ $log->description }}

                                    @if ($log->old_values || $log->new_values)
                                        <details>
                                            <summary>Details</summary>
                                            @if ($log->old_values)
                                                <pre class="audit-values">Before: {{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @endif
                                            @if ($log->new_values)
                                                <pre class="audit-values">After: {{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @endif
                                        </details>
                                    @endif
                                </td>
                                <td class="text-muted-sm nowrap">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="panel-footer">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection
