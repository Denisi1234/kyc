@php
    $isEdit = isset($event) && $event->exists;
    $isPublished = old('is_published', $isEdit ? $event->is_published : false);
    $isFeatured = old('is_featured', $isEdit ? $event->is_featured : false);
    $startsAt = old('starts_at', $isEdit ? $event->starts_at?->format('Y-m-d\TH:i') : '');
    $endsAt = old('ends_at', $isEdit ? $event->ends_at?->format('Y-m-d\TH:i') : '');
@endphp

<div class="grid-2">
    <div>
        <h3 class="h6 mb-3">Event details</h3>

        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="required-mark">*</span></label>
            <input
                id="title"
                name="title"
                type="text"
                class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $event->title ?? '') }}"
                maxlength="160"
                required
            >
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">URL slug</label>
            <input
                id="slug"
                name="slug"
                type="text"
                class="form-control @error('slug') is-invalid @enderror"
                value="{{ old('slug', $event->slug ?? '') }}"
                maxlength="180"
                placeholder="Generated from the title"
            >
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Public address: /events/{{ old('slug', $event->slug ?? 'your-event') }}</div>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input
                id="category"
                name="category"
                type="text"
                class="form-control @error('category') is-invalid @enderror"
                value="{{ old('category', $event->category ?? '') }}"
                maxlength="80"
                placeholder="Concert, Worship, Rehearsal, Community"
            >
            @error('category')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="summary" class="form-label">Short summary</label>
            <textarea
                id="summary"
                name="summary"
                class="form-control @error('summary') is-invalid @enderror"
                rows="3"
                maxlength="600"
                placeholder="A one or two sentence description for event cards."
            >{{ old('summary', $event->summary ?? '') }}</textarea>
            @error('summary')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Full description</label>
            <textarea
                id="description"
                name="description"
                class="form-control @error('description') is-invalid @enderror"
                rows="10"
                placeholder="Write the full event details. Leave a blank line between paragraphs."
            >{{ old('description', $event->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Paragraphs are preserved on the public event page.</div>
        </div>
    </div>

    <div>
        <h3 class="h6 mb-3">Schedule</h3>

        <div class="mb-3">
            <label for="starts_at" class="form-label">Starts at <span class="required-mark">*</span></label>
            <input
                id="starts_at"
                name="starts_at"
                type="datetime-local"
                class="form-control @error('starts_at') is-invalid @enderror"
                value="{{ $startsAt }}"
                required
            >
            @error('starts_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="ends_at" class="form-label">Ends at</label>
            <input
                id="ends_at"
                name="ends_at"
                type="datetime-local"
                class="form-control @error('ends_at') is-invalid @enderror"
                value="{{ $endsAt }}"
            >
            @error('ends_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <h3 class="h6 mb-3 mt-4">Location</h3>

        <div class="mb-3">
            <label for="venue" class="form-label">Venue</label>
            <input
                id="venue"
                name="venue"
                type="text"
                class="form-control @error('venue') is-invalid @enderror"
                value="{{ old('venue', $event->venue ?? '') }}"
                maxlength="160"
                placeholder="Kijitonyama Lutheran Church"
            >
            @error('venue')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input
                id="address"
                name="address"
                type="text"
                class="form-control @error('address') is-invalid @enderror"
                value="{{ old('address', $event->address ?? '') }}"
                maxlength="255"
                placeholder="Kijitonyama, Dar es Salaam"
            >
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <h3 class="h6 mb-3 mt-4">Event image</h3>

        @if ($isEdit && $event->image_path)
            <div class="mb-3">
                <img class="event-image-preview" src="{{ $event->image_url }}" alt="Current event image">
            </div>
        @endif

        <div class="mb-3">
            <label for="image" class="form-label">Replace image</label>
            <input
                id="image"
                name="image"
                type="file"
                class="form-control @error('image') is-invalid @enderror"
                accept="image/jpeg,image/png,image/webp"
            >
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">JPG, PNG or WebP, up to 4 MB. Landscape images work best.</div>
        </div>
    </div>
</div>

<div class="divider"></div>

<div class="grid-2">
    <div class="form-check form-switch mb-3">
        <input type="hidden" name="is_published" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            role="switch"
            name="is_published"
            id="is_published"
            value="1"
            @checked($isPublished)
        >
        <label class="form-check-label" for="is_published">
            Published on the public website
        </label>
    </div>

    <div class="form-check form-switch mb-3">
        <input type="hidden" name="is_featured" value="0">
        <input
            class="form-check-input"
            type="checkbox"
            role="switch"
            name="is_featured"
            id="is_featured"
            value="1"
            @checked($isFeatured)
        >
        <label class="form-check-label" for="is_featured">
            Feature this event on the homepage
        </label>
    </div>
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save event' }}</button>
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
