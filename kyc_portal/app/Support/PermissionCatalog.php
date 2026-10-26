<?php

namespace App\Support;

use App\Enums\RoleName;

class PermissionCatalog
{
    public const GROUPS = [
        'Overview' => [
            'dashboard.view',
        ],
        'Profile' => [
            'profile.update',
        ],
        'Applications' => [
            'applications.view',
            'applications.screen',
            'applications.approve',
            'applications.delete',
        ],
        'Members' => [
            'members.view',
            'members.manage',
            'members.export',
        ],
        'Positions' => [
            'positions.view',
            'positions.manage',
        ],
        'Events' => [
            'events.view',
            'events.manage',
        ],
        'Attendance' => [
            'attendance.view',
            'attendance.mark',
            'attendance.manage',
            'attendance.self.view',
        ],
        'Contributions' => [
            'contributions.view',
            'contributions.configure',
            'contributions.record',
            'contributions.verify',
            'contributions.adjust',
            'contributions.approve',
            'contributions.self.view',
            'contributions.self.claim',
        ],
        'Receipts' => [
            'receipts.issue',
        ],
        'Reports' => [
            'reports.view',
            'reports.export',
            'reports.finance.view',
            'reports.finance.export',
        ],
        'Announcements' => [
            'announcements.view',
            'announcements.manage',
        ],
        'Gallery' => [
            'gallery.view',
            'gallery.manage',
        ],
        'Users' => [
            'users.view',
            'users.manage',
        ],
        'Roles' => [
            'roles.view',
            'roles.manage',
        ],
        'Settings' => [
            'settings.view',
            'settings.manage',
        ],
        'Audit' => [
            'audit.view',
        ],
        'Public content' => [
            'content.view',
            'content.manage',
        ],
    ];

    public const LABELS = [
        'dashboard.view' => 'View dashboard',
        'profile.update' => 'Update own profile',
        'applications.view' => 'View applications',
        'applications.screen' => 'Screen applications',
        'applications.approve' => 'Approve applications',
        'applications.delete' => 'Delete applications',
        'members.view' => 'View members',
        'members.manage' => 'Manage members',
        'members.export' => 'Export members',
        'positions.view' => 'View positions',
        'positions.manage' => 'Manage positions',
        'events.view' => 'View events',
        'events.manage' => 'Manage events',
        'attendance.view' => 'View attendance',
        'attendance.mark' => 'Mark attendance',
        'attendance.manage' => 'Manage attendance',
        'attendance.self.view' => 'View own attendance',
        'contributions.view' => 'View contributions',
        'contributions.configure' => 'Configure contribution categories',
        'contributions.record' => 'Record contributions',
        'contributions.verify' => 'Verify contribution claims',
        'contributions.adjust' => 'Request adjustments and reversals',
        'contributions.approve' => 'Approve adjustments and waivers',
        'contributions.self.view' => 'View own contributions',
        'contributions.self.claim' => 'Submit own contribution claims',
        'receipts.issue' => 'Issue receipts',
        'reports.view' => 'View reports',
        'reports.export' => 'Export reports',
        'reports.finance.view' => 'View finance reports',
        'reports.finance.export' => 'Export finance reports',
        'announcements.view' => 'View announcements',
        'announcements.manage' => 'Manage announcements',
        'gallery.view' => 'View gallery',
        'gallery.manage' => 'Manage gallery',
        'users.view' => 'View users',
        'users.manage' => 'Manage users',
        'roles.view' => 'View roles',
        'roles.manage' => 'Manage roles and permissions',
        'settings.view' => 'View settings',
        'settings.manage' => 'Manage settings',
        'audit.view' => 'View audit trail',
        'content.view' => 'View public content',
        'content.manage' => 'Manage public content',
    ];

    public static function all(): array
    {
        return array_values(array_merge(...array_values(self::GROUPS)));
    }

    public static function label(string $permission): string
    {
        return self::LABELS[$permission] ?? str($permission)->replace(['.', '_'], ' ')->headline()->toString();
    }

    public static function rolePermissions(): array
    {
        $member = [
            'dashboard.view',
            'profile.update',
            'contributions.self.view',
            'contributions.self.claim',
            'events.view',
            'attendance.self.view',
            'announcements.view',
            'gallery.view',
        ];

        return [
            RoleName::Member->value => $member,

            RoleName::SectionLeader->value => array_values(array_unique(array_merge($member, [
                'members.view',
                'attendance.view',
                'attendance.mark',
                'reports.view',
            ]))),

            RoleName::ChoirMaster->value => array_values(array_unique(array_merge($member, [
                'members.view',
                'events.view',
                'events.manage',
                'attendance.view',
                'attendance.mark',
                'attendance.manage',
                'reports.view',
                'reports.export',
                'announcements.view',
                'gallery.view',
            ]))),

            RoleName::Secretary->value => [
                'dashboard.view',
                'profile.update',
                'applications.view',
                'applications.screen',
                'members.view',
                'members.manage',
                'members.export',
                'positions.view',
                'positions.manage',
                'events.view',
                'attendance.view',
                'announcements.view',
                'announcements.manage',
                'gallery.view',
                'gallery.manage',
                'reports.view',
                'reports.export',
            ],

            RoleName::Treasurer->value => [
                'dashboard.view',
                'profile.update',
                'members.view',
                'events.view',
                'contributions.view',
                'contributions.configure',
                'contributions.record',
                'contributions.verify',
                'contributions.adjust',
                'receipts.issue',
                'reports.view',
                'reports.export',
                'reports.finance.view',
                'reports.finance.export',
                'announcements.view',
            ],

            RoleName::Chairperson->value => [
                'dashboard.view',
                'profile.update',
                'applications.view',
                'applications.approve',
                'members.view',
                'positions.view',
                'events.view',
                'contributions.view',
                'contributions.approve',
                'reports.view',
                'reports.export',
                'reports.finance.view',
                'announcements.view',
                'audit.view',
            ],

            RoleName::SystemAdmin->value => [
                'dashboard.view',
                'profile.update',
                'events.view',
                'events.manage',
                'users.view',
                'users.manage',
                'roles.view',
                'roles.manage',
                'settings.view',
                'settings.manage',
                'audit.view',
            ],
        ];
    }
}
