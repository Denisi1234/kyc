<?php

namespace App\Enums;

enum AuditEvent: string
{
    case AuthLogin = 'auth.login';
    case AuthLoginFailed = 'auth.login_failed';
    case AuthLogout = 'auth.logout';
    case PasswordReset = 'password.reset';
    case PasswordChanged = 'password.changed';
    case ProfileUpdated = 'profile.updated';
    case UserCreated = 'user.created';
    case UserUpdated = 'user.updated';
    case UserDeactivated = 'user.deactivated';
    case UserActivated = 'user.activated';
    case RoleAssigned = 'role.assigned';
    case RoleRemoved = 'role.removed';
    case RolePermissionsUpdated = 'role.permissions_updated';
    case SettingsUpdated = 'settings.updated';
    case EventCreated = 'event.created';
    case EventUpdated = 'event.updated';
    case EventPublished = 'event.published';
    case EventUnpublished = 'event.unpublished';
    case EventDeleted = 'event.deleted';
    case AdminBootstrapped = 'admin.bootstrap';

    public function label(): string
    {
        return match ($this) {
            self::AuthLogin => 'Signed in',
            self::AuthLoginFailed => 'Failed sign-in',
            self::AuthLogout => 'Signed out',
            self::PasswordReset => 'Password reset',
            self::PasswordChanged => 'Password changed',
            self::ProfileUpdated => 'Profile updated',
            self::UserCreated => 'User created',
            self::UserUpdated => 'User updated',
            self::UserDeactivated => 'User deactivated',
            self::UserActivated => 'User activated',
            self::RoleAssigned => 'Role assigned',
            self::RoleRemoved => 'Role removed',
            self::RolePermissionsUpdated => 'Role permissions updated',
            self::SettingsUpdated => 'Settings updated',
            self::EventCreated => 'Event created',
            self::EventUpdated => 'Event updated',
            self::EventPublished => 'Event published',
            self::EventUnpublished => 'Event unpublished',
            self::EventDeleted => 'Event deleted',
            self::AdminBootstrapped => 'Administrator created',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(static fn (self $event) => [$event->value => $event->label()])
            ->all();
    }
}
