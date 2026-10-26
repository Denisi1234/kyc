<?php

namespace App\Enums;

enum RoleName: string
{
    case Member = 'member';
    case SectionLeader = 'section_leader';
    case ChoirMaster = 'choir_master';
    case Secretary = 'secretary';
    case Treasurer = 'treasurer';
    case Chairperson = 'chairperson';
    case SystemAdmin = 'system_admin';

    public function label(): string
    {
        return match ($this) {
            self::Member => 'Member',
            self::SectionLeader => 'Section Leader',
            self::ChoirMaster => 'Choir Master',
            self::Secretary => 'Secretary',
            self::Treasurer => 'Treasurer',
            self::Chairperson => 'Chairperson',
            self::SystemAdmin => 'System Admin',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Member => 'Access to personal contributions, events, attendance and choir notices.',
            self::SectionLeader => 'Leads a vocal section and marks attendance for that section.',
            self::ChoirMaster => 'Manages rehearsals, events and choir attendance.',
            self::Secretary => 'Screens applications and maintains member records.',
            self::Treasurer => 'Manages contribution categories, claims, postings and receipts.',
            self::Chairperson => 'Governance oversight and approvals for applications and finance adjustments.',
            self::SystemAdmin => 'Manages portal users, roles, settings and the audit trail.',
        };
    }

    public function priority(): int
    {
        return match ($this) {
            self::SystemAdmin => 0,
            self::Chairperson => 1,
            self::Treasurer => 2,
            self::Secretary => 3,
            self::ChoirMaster => 4,
            self::SectionLeader => 5,
            self::Member => 6,
        };
    }

    public static function values(): array
    {
        return array_map(static fn (self $role) => $role->value, self::cases());
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(static fn (self $role) => [$role->value => $role->label()])
            ->all();
    }
}
