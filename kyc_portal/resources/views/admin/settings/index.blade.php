@extends('layouts.portal')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Settings</h1>
            <p>Organisation, finance and access defaults. Changes are audited and apply to future portal activity.</p>
        </div>
        @can('settings.manage')
            <div class="heading-actions">
                <button type="submit" form="settings-form" class="btn btn-primary">Save settings</button>
            </div>
        @endcan
    </div>

    <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}" novalidate>
        @csrf
        @method('PUT')

        @foreach ($groups as $groupKey => $group)
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">{{ $group['label'] }}</h2>
                        <p class="panel-subtitle">{{ $group['description'] }}</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="grid-2">
                        @foreach ($group['fields'] as $key => $field)
                            @php
                                $safe = str_replace('.', '__', $key);
                                $setting = $settings->get($key);
                                $value = old('settings.'.$safe, $setting?->typedValue() ?? ($field['default'] ?? null));
                                $type = $field['type'] ?? 'text';
                                $required = str_contains((string) ($field['rules'] ?? ''), 'required') && $type !== 'boolean';
                                $errorKey = 'settings.'.$safe;
                            @endphp

                            <div class="{{ $type === 'textarea' ? 'grid-span-2' : '' }}">
                                @if ($type === 'boolean')
                                    <input type="hidden" name="settings[{{ $safe }}]" value="0">
                                    <div class="form-check form-switch mt-4">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            name="settings[{{ $safe }}]"
                                            id="setting-{{ $safe }}"
                                            value="1"
                                            @checked((bool) $value)
                                        >
                                        <label class="form-check-label" for="setting-{{ $safe }}">
                                            {{ $field['label'] }}
                                        </label>
                                    </div>
                                @else
                                    <label for="setting-{{ $safe }}" class="form-label">
                                        {{ $field['label'] }}
                                        @if ($required)<span class="required-mark">*</span>@endif
                                    </label>

                                    @if ($type === 'textarea')
                                        <textarea
                                            id="setting-{{ $safe }}"
                                            name="settings[{{ $safe }}]"
                                            class="form-control @error($errorKey) is-invalid @enderror"
                                            rows="3"
                                        >{{ $value }}</textarea>
                                    @elseif ($type === 'select')
                                        <select
                                            id="setting-{{ $safe }}"
                                            name="settings[{{ $safe }}]"
                                            class="form-select @error($errorKey) is-invalid @enderror"
                                        >
                                            @foreach (($field['options'] ?? []) as $optionValue => $optionLabel)
                                                <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input
                                            id="setting-{{ $safe }}"
                                            name="settings[{{ $safe }}]"
                                            type="{{ $type === 'integer' ? 'number' : $type }}"
                                            class="form-control @error($errorKey) is-invalid @enderror"
                                            value="{{ $value }}"
                                            @if ($type === 'integer') inputmode="numeric" @endif
                                            @if ($field['encrypted'] ?? false) placeholder="Leave blank to keep current value" @endif
                                        >
                                    @endif

                                    @error($errorKey)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach

        @can('settings.manage')
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Save settings</button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        @endcan
    </form>
@endsection
