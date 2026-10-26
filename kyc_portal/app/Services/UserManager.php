<?php

namespace App\Services;

use App\Enums\AuditEvent;
use App\Enums\RoleName;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserManager
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly AdminGuard $admins,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $roles
     */
    public function create(array $data, array $roles, ?User $actor = null): User
    {
        $roles = $this->validateRoles($roles);

        return DB::transaction(function () use ($data, $roles, $actor) {
            $email = $this->normalizeEmail($data['email'] ?? null);
            $phone = PhoneNumber::normalize($data['phone'] ?? null);

            $user = User::create([
                'name' => trim($data['name']),
                'email' => $email,
                'phone' => $phone,
                'password' => $data['password'],
                'is_active' => (bool) ($data['is_active'] ?? true),
                'must_change_password' => (bool) ($data['must_change_password'] ?? true),
                'email_verified_at' => $email ? now() : null,
            ]);

            $user->syncRoles($roles);

            $this->audit->log(
                AuditEvent::UserCreated,
                $user,
                "Created user {$user->name}.",
                [],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'roles' => $roles,
                    'is_active' => $user->is_active,
                ],
                $actor,
            );

            return $user->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $roles
     */
    public function update(User $user, array $data, array $roles, ?User $actor = null): User
    {
        $roles = $this->validateRoles($roles);

        return DB::transaction(function () use ($user, $data, $roles, $actor) {
            $user->load('roles');

            $oldRoles = $user->getRoleNames()->values()->all();
            $old = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $oldRoles,
                'is_active' => $user->is_active,
            ];

            $newIsActive = (bool) ($data['is_active'] ?? false);
            $losesAdmin = in_array(RoleName::SystemAdmin->value, $oldRoles, true)
                && ! in_array(RoleName::SystemAdmin->value, $roles, true);

            if ($losesAdmin) {
                $this->admins->ensureCanRemoveAdminRole($user, $actor);
            }

            if ($user->is_active && ! $newIsActive) {
                $this->admins->ensureCanDeactivate($user, $actor);
            }

            $email = $this->normalizeEmail($data['email'] ?? null);
            $phone = PhoneNumber::normalize($data['phone'] ?? null);

            $user->fill([
                'name' => trim($data['name']),
                'email' => $email,
                'phone' => $phone,
                'is_active' => $newIsActive,
                'must_change_password' => (bool) ($data['must_change_password'] ?? false),
            ]);

            if ($email !== $old['email']) {
                $user->email_verified_at = null;
            }

            if (! empty($data['password'])) {
                $user->password = $data['password'];
                $user->must_change_password = (bool) ($data['must_change_password'] ?? false);
            }

            $user->save();
            $user->syncRoles($roles);

            $new = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $roles,
                'is_active' => $user->is_active,
            ];

            $changes = collect($new)
                ->filter(static fn (mixed $value, string $key) => $value !== $old[$key])
                ->all();

            if ($changes !== []) {
                $this->audit->log(
                    AuditEvent::UserUpdated,
                    $user,
                    "Updated user {$user->name}.",
                    Arr::only($old, array_keys($changes)),
                    $changes,
                    $actor,
                );
            }

            foreach (array_diff($roles, $oldRoles) as $role) {
                $this->audit->log(
                    AuditEvent::RoleAssigned,
                    $user,
                    "Assigned {$this->roleLabel($role)} role to {$user->name}.",
                    [],
                    ['role' => $role],
                    $actor,
                );
            }

            foreach (array_diff($oldRoles, $roles) as $role) {
                $this->audit->log(
                    AuditEvent::RoleRemoved,
                    $user,
                    "Removed {$this->roleLabel($role)} role from {$user->name}.",
                    ['role' => $role],
                    [],
                    $actor,
                );
            }

            return $user->refresh();
        });
    }

    public function setActive(User $user, bool $active, ?User $actor = null): User
    {
        if (! $active && $user->is_active) {
            $this->admins->ensureCanDeactivate($user, $actor);
        }

        $old = $user->is_active;
        $user->is_active = $active;
        $user->save();

        $this->audit->log(
            $active ? AuditEvent::UserActivated : AuditEvent::UserDeactivated,
            $user,
            ($active ? 'Activated' : 'Deactivated')." user {$user->name}.",
            ['is_active' => $old],
            ['is_active' => $active],
            $actor,
        );

        return $user->refresh();
    }

    /**
     * @param  list<string>  $roles
     * @return list<string>
     */
    private function validateRoles(array $roles): array
    {
        $roles = array_values(array_unique(array_filter($roles)));

        if ($roles === []) {
            throw ValidationException::withMessages([
                'roles' => 'Assign at least one role.',
            ]);
        }

        $valid = Role::query()
            ->whereIn('name', $roles)
            ->pluck('name')
            ->all();

        if (count($valid) !== count($roles)) {
            throw ValidationException::withMessages([
                'roles' => 'One or more selected roles are invalid.',
            ]);
        }

        return $valid;
    }

    private function normalizeEmail(?string $email): ?string
    {
        $email = trim((string) $email);

        return $email === '' ? null : mb_strtolower($email);
    }

    private function roleLabel(string $role): string
    {
        return RoleName::tryFrom($role)?->label() ?? $role;
    }
}
