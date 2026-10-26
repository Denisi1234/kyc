@extends('layouts.portal')

@section('title', 'Events')
@section('page-title', 'Events')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Events</h1>
            <p>Publish concerts, services, rehearsals and community activities to the public website.</p>
        </div>
        @can('events.manage')
            <div class="heading-actions">
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    <x-icon name="plus" :size="16" class="me-1" />
                    Add event
                </a>
            </div>
        @endcan
    </div>

    <div class="grid-4 mb-4">
        <div class="stat">
            <div class="stat-label">All events</div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
            <div class="stat-note">Including drafts and past events</div>
        </div>
        <div class="stat">
            <div class="stat-label">Upcoming</div>
            <div class="stat-value">{{ number_format($stats['upcoming']) }}</div>
            <div class="stat-note">From today onwards</div>
        </div>
        <div class="stat">
            <div class="stat-label">Published</div>
            <div class="stat-value">{{ number_format($stats['published']) }}</div>
            <div class="stat-note">Visible on the public site</div>
        </div>
        <div class="stat">
            <div class="stat-label">Drafts</div>
            <div class="stat-value">{{ number_format($stats['drafts']) }}</div>
            <div class="stat-note">Not yet public</div>
        </div>
    </div>

    <section class="panel">
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.events.index') }}" class="filter-bar">
                <div class="search-field">
                    <label for="search" class="form-label">Search</label>
                    <input
                        id="search"
                        name="search"
                        type="search"
                        class="form-control"
                        value="{{ $filters['search'] }}"
                        placeholder="Title, summary or venue"
                    >
                </div>
                <div>
                    <label for="status" class="form-label">Date</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">All dates</option>
                        <option value="upcoming" @selected($filters['status'] === 'upcoming')>Upcoming</option>
                        <option value="past" @selected($filters['status'] === 'past')>Past</option>
                    </select>
                </div>
                <div>
                    <label for="visibility" class="form-label">Visibility</label>
                    <select id="visibility" name="visibility" class="form-select">
                        <option value="">All</option>
                        <option value="published" @selected($filters['visibility'] === 'published')>Published</option>
                        <option value="draft" @selected($filters['visibility'] === 'draft')>Draft</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary">Filter</button>
                    @if ($filters['search'] !== '' || $filters['status'] !== '' || $filters['visibility'] !== '')
                        <a href="{{ route('admin.events.index') }}" class="btn btn-link px-1">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="panel">
        @if ($events->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><x-icon name="calendar" /></div>
                <h3>No events found</h3>
                <p>Adjust the filters or create the first event for the public site.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Event</th>
                            <th scope="col">Date &amp; time</th>
                            <th scope="col">Venue</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="event-thumb" src="{{ $event->image_url }}" alt="" width="46" height="46" loading="lazy">
                                        <div>
                                            <div class="cell-title">{{ $event->title }}</div>
                                            <div class="cell-meta">
                                                {{ $event->category ?? 'Choir event' }}
                                                @if ($event->is_featured)
                                                    · Featured
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $event->starts_at->format('d M Y') }}</div>
                                    <div class="cell-meta">{{ $event->starts_at->format('g:i A') }}</div>
                                </td>
                                <td class="text-muted-sm">{{ $event->venue ?? '—' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if ($event->isUpcoming())
                                            <span class="badge badge-role">Upcoming</span>
                                        @else
                                            <span class="badge badge-neutral">Past</span>
                                        @endif
                                        @if ($event->is_published)
                                            <span class="badge badge-active">Published</span>
                                        @else
                                            <span class="badge badge-inactive">Draft</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                        <a href="{{ route('public.events.show', $event) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">View</a>
                                        @can('events.manage')
                                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @if ($event->is_published)
                                                <form method="POST" action="{{ route('admin.events.unpublish', $event) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Unpublish</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.events.publish', $event) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">Publish</button>
                                                </form>
                                            @endif
                                            <form
                                                method="POST"
                                                action="{{ route('admin.events.destroy', $event) }}"
                                                data-confirm="Delete {{ $event->title }}? This removes it from the public site."
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($events->hasPages())
                <div class="panel-footer">
                    {{ $events->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection
