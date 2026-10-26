<?php

namespace App\Services;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AdminGuard
{
    public function activeAdminCount(): int
    {
        return User::query()
            ->role(RoleName::SystemAdmin->value)
            ->where('is_active', true)
            ->count();
    }

    public function hasMinimumAdmins(): bool
    {
        return $this->activeAdminCount() >= 2;
    }

    public function ensureCanRemoveAdminRole(User $target, ?User $actor = null): void
    {
        if (! $target->hasRole(RoleName::SystemAdmin->value)) {
            return;
        }

        if ($actor?->is($target)) {
            throw ValidationException::withMessages([
                'roles' => 'You cannot remove your own System Admin role. Ask another administrator to make this change.',
            ]);
        }

        if ($this->activeAdminCount() <= 1) {
            throw ValidationException::withMessages([
                'roles' => 'The last active System Admin cannot lose the administrator role. Add another administrator first.',
            ]);
        }
    }

    public function ensureCanDeactivate(User $target, ?User $actor = null): void
    {
        if ($actor?->is($target)) {
            throw ValidationException::withMessages([
                'is_active' => 'You cannot deactivate your own account.',
            ]);
        }

        if ($target->hasRole(RoleName::SystemAdmin->value) && $this->activeAdminCount() <= 1) {
            throw ValidationException::withMessages([
                'is_active' => 'The last active System Admin cannot be deactivated. Add another administrator first.',
            ]);
        }
    }
}
