@php
    $isEdit = isset($user) && $user?->exists;
    $selected = old('roles', $selectedRoles ?? []);
    $isActive = old('is_active', $isEdit ? $user->is_active : true);
    $mustChange = old('must_change_password', $isEdit ? $user->must_change_password : true);
@endphp

<div class="grid-2">
    <div>
        <h3 class="h6 mb-3">Account details</h3>

        <div class="mb-3">
            <label for="name" class="form-label">Full name <span class="required-mark">*</span></label>
            <input
                id="name"
                name="name"
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name ?? '') }}"
                autocomplete="name"
                required
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email ?? '') }}"
                autocomplete="email"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Email or phone is required. Email is needed for password reset.</div>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone number</label>
            <input
                id="phone"
                name="phone"
                type="tel"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $user->phone ?? '') }}"
                autocomplete="tel"
                placeholder="+255 753 423 251"
            >
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Tanzanian numbers are normalised to +255 format.</div>
        </div>
    </div>

    <div>
        <h3 class="h6 mb-3">{{ $isEdit ? 'Password' : 'Temporary password' }}</h3>

        <div class="mb-3">
            <label for="password" class="form-label">
                {{ $isEdit ? 'New password' : 'Temporary password' }}
                @unless ($isEdit)<span class="required-mark">*</span>@endunless
            </label>
            <input
                id="password"
                name="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                autocomplete="new-password"
                @unless ($isEdit) required @endunless
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
                @if ($isEdit)
                    Leave blank to keep the current password.
                @else
                    At least 12 characters with mixed case, a number and a symbol.
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="form-control"
                autocomplete="new-password"
                @unless ($isEdit) required @endunless
            >
        </div>

        <div class="form-check form-switch">
            <input type="hidden" name="must_change_password" value="0">
            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="must_change_password"
                id="must_change_password"
                value="1"
                @checked($mustChange)
            >
            <label class="form-check-label" for="must_change_password">
                Require password change at next sign-in
            </label>
        </div>
    </div>
</div>

<div class="divider"></div>

<fieldset>
    <legend class="h6 mb-1">Roles <span class="required-mark">*</span></legend>
    <p class="text-muted-sm mb-3">A user may hold more than one role. Permissions are the union of all assigned roles.</p>

    @error('roles')
        <div class="alert alert-danger py-2">{{ $message }}</div>
    @enderror

    <div class="grid-2">
        @foreach ($roles as $role)
            <div class="form-check mb-2">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="roles[]"
                    id="role-{{ $role['value'] }}"
                    value="{{ $role['value'] }}"
                    @checked(in_array($role['value'], $selected, true))
                >
                <label class="form-check-label" for="role-{{ $role['value'] }}">
                    <strong class="d-block">{{ $role['label'] }}</strong>
                    <span class="text-muted-sm">{{ $role['description'] }}</span>
                </label>
            </div>
        @endforeach
    </div>
</fieldset>

<div class="divider"></div>

<div class="form-check form-switch mb-4">
    <input type="hidden" name="is_active" value="0">
    <input
        class="form-check-input"
        type="checkbox"
        role="switch"
        name="is_active"
        id="is_active"
        value="1"
        @checked($isActive)
    >
    <label class="form-check-label" for="is_active">
        Account is active and can sign in
    </label>
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save user' }}</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
