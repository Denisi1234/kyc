@extends('layouts.portal')

@section('title', 'Edit event')
@section('page-title', 'Edit event')

@section('content')
    <div class="page-heading">
        <div>
            <h1>{{ $event->title }}</h1>
            <p>
                {{ $event->is_published ? 'Published' : 'Draft' }}
                · {{ $event->starts_at->format('l, j F Y \a\t g:i A') }}
            </p>
        </div>
        <div class="heading-actions">
            @if ($event->is_published)
                <a href="{{ route('public.events.show', $event) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary">View on site</a>
            @endif
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Back to events</a>
        </div>
    </div>

    <section class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PATCH')
                @include('admin.events._form', [
                    'submitLabel' => 'Save changes',
                ])
            </form>
        </div>
    </section>

    @can('events.manage')
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Event visibility</h2>
                    <p class="panel-subtitle">Publishing makes this event visible on the public website immediately.</p>
                </div>
            </div>
            <div class="panel-body d-flex flex-wrap gap-2">
                @if ($event->is_published)
                    <form method="POST" action="{{ route('admin.events.unpublish', $event) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">Move to draft</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.events.publish', $event) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Publish event</button>
                    </form>
                @endif

                <form
                    method="POST"
                    action="{{ route('admin.events.destroy', $event) }}"
                    data-confirm="Delete {{ $event->title }}? This cannot be undone from the portal."
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete event</button>
                </form>
            </div>
        </section>
    @endcan
@endsection
