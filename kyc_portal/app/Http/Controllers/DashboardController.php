<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Services\AdminGuard;
use App\Support\PermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __invoke(Request $request, AdminGuard $admins): View
    {
        $user = $request->user()->load('roles');

        $canViewAdministration = $user->can('users.view');
        $canViewAudit = $user->can('audit.view');

        $capabilities = $user->getAllPermissions()
            ->pluck('name')
            ->map(static fn (string $permission) => PermissionCatalog::label($permission))
            ->values()
            ->all();

        return view('dashboard', [
            'user' => $user,
            'organizationName' => Setting::getValue('organization.name', 'Kijitonyama Youth Choir'),
            'organizationPhone' => Setting::getValue('organization.phone'),
            'capabilities' => array_slice($capabilities, 0, 8),
            'activeUserCount' => $canViewAdministration ? User::query()->where('is_active', true)->count() : null,
            'adminCount' => $canViewAdministration ? $admins->activeAdminCount() : null,
            'roleCount' => $canViewAdministration ? Role::query()->count() : null,
            'hasMinimumAdmins' => $canViewAdministration ? $admins->hasMinimumAdmins() : null,
            'recentAudit' => $canViewAudit
                ? AuditLog::query()->with('actor')->latest('id')->limit(6)->get()
                : collect(),
        ]);
    }
}
