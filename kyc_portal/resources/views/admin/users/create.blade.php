@extends('layouts.portal')

@section('title', 'Add user')
@section('page-title', 'Add user')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Add user</h1>
            <p>Create a portal account for a member or leader. Share the temporary password securely.</p>
        </div>
        <div class="heading-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back to users</a>
        </div>
    </div>

    <section class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
                @csrf
                @include('admin.users._form', [
                    'submitLabel' => 'Create user',
                ])
            </form>
        </div>
    </section>
@endsection
