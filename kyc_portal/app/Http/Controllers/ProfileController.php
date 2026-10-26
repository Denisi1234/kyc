<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load('roles'),
        ]);
    }

    public function update(ProfileUpdateRequest $request, AuditLogger $audit): RedirectResponse
    {
        $user = $request->user();
        $old = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $new = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        $changes = collect($new)
            ->filter(static fn (mixed $value, string $key) => $value !== $old[$key])
            ->all();

        if ($changes !== []) {
            $audit->log(
                AuditEvent::ProfileUpdated,
                $user,
                "{$user->name} updated their profile.",
                array_intersect_key($old, $changes),
                $changes,
                $user,
            );
        }

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request, AuditLogger $audit): RedirectResponse
    {
        $user = $request->user();
        $user->forceFill([
            'password' => $request->validated('password'),
            'must_change_password' => false,
        ])->save();

        $audit->log(
            AuditEvent::PasswordChanged,
            $user,
            "{$user->name} changed their password.",
            [],
            [],
            $user,
        );

        $request->session()->regenerate();

        return back()->with('status', 'Password changed.');
    }
}
