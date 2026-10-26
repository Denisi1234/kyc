@extends('layouts.portal')

@section('title', 'Add event')
@section('page-title', 'Add event')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Add event</h1>
            <p>Create a public event. Save it as a draft until the details are confirmed.</p>
        </div>
        <div class="heading-actions">
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Back to events</a>
        </div>
    </div>

    <section class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @include('admin.events._form', [
                    'submitLabel' => 'Create event',
                ])
            </form>
        </div>
    </section>
@endsection
