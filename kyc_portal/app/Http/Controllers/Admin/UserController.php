<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->string('search')),
            'role' => (string) $request->string('role'),
            'status' => (string) $request->string('status'),
        ];

        $users = User::query()
            ->with('roles')
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $search = '%'.$filters['search'].'%';
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('phone', 'like', $search);
                });
            })
            ->when(in_array($filters['role'], RoleName::values(), true), function ($query) use ($filters) {
                $query->role($filters['role']);
            })
            ->when($filters['status'] === 'active', fn ($query) => $query->where('is_active', true))
            ->when($filters['status'] === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $filters,
            'roles' => $this->roleOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => $this->roleOptions(),
        ]);
    }

    public function store(StoreUserRequest $request, UserManager $users): RedirectResponse
    {
        $validated = $request->validated();
        $roles = $validated['roles'];
        unset($validated['roles']);

        $user = $users->create($validated, $roles, $request->user());

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'User created. Share the temporary password securely; it must be changed at first sign-in.');
    }

    public function edit(User $user): View
    {
        $user->load('roles');

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $this->roleOptions(),
            'selectedRoles' => $user->getRoleNames()->values()->all(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UserManager $users): RedirectResponse
    {
        $validated = $request->validated();
        $roles = $validated['roles'];
        unset($validated['roles']);

        $users->update($user, $validated, $roles, $request->user());

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'User updated.');
    }

    public function activate(Request $request, User $user, UserManager $users): RedirectResponse
    {
        $users->setActive($user, true, $request->user());

        return back()->with('status', "{$user->name} has been activated.");
    }

    public function deactivate(Request $request, User $user, UserManager $users): RedirectResponse
    {
        $users->setActive($user, false, $request->user());

        return back()->with('status', "{$user->name} has been deactivated.");
    }

    /**
     * @return list<array{value: string, label: string, description: string}>
     */
    private function roleOptions(): array
    {
        return collect(RoleName::cases())
            ->map(static fn (RoleName $role) => [
                'value' => $role->value,
                'label' => $role->label(),
                'description' => $role->description(),
            ])
            ->values()
            ->all();
    }
}
