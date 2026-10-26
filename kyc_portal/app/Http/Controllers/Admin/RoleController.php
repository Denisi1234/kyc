<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AuditEvent;
use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Support\PermissionCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->with('permissions')
            ->withCount('users')
            ->get()
            ->sortBy(static fn (Role $role) => array_search($role->name, RoleName::values(), true))
            ->values();

        return view('admin.roles.index', [
            'roles' => $roles,
            'groups' => PermissionCatalog::GROUPS,
        ]);
    }

    public function update(Request $request, Role $role, AuditLogger $audit): RedirectResponse
    {
        if ($role->name === RoleName::SystemAdmin->value) {
            return back()->withErrors([
                'permissions' => 'The System Admin role is fixed and cannot be edited.',
            ]);
        }

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', PermissionCatalog::all())],
        ]);

        $old = $role->permissions->pluck('name')->sort()->values()->all();
        $permissions = array_values(array_unique($request->input('permissions', [])));
        sort($permissions);

        $role->syncPermissions($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $added = array_values(array_diff($permissions, $old));
        $removed = array_values(array_diff($old, $permissions));

        if ($added !== [] || $removed !== []) {
            $audit->log(
                AuditEvent::RolePermissionsUpdated,
                $role,
                "Updated permissions for the {$role->name} role.",
                ['removed' => $removed],
                ['added' => $added],
                $request->user(),
            );
        }

        return back()->with('status', "Permissions updated for {$role->name}.");
    }
}
