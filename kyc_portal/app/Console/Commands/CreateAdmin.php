<?php

namespace App\Console\Commands;

use App\Enums\AuditEvent;
use App\Enums\RoleName;
use App\Models\Setting;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\UserManager;
use App\Support\PhoneNumber;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class CreateAdmin extends Command
{
    protected $signature = 'portal:create-admin
                            {--name= : Full name}
                            {--email= : Email address}
                            {--phone= : Phone number}
                            {--password= : Temporary password}';

    protected $description = 'Create the first System Admin account for the portal';

    public function handle(UserManager $users, AuditLogger $audit): int
    {
        $name = $this->option('name') ?: $this->ask('Full name');
        $email = $this->option('email') ?: $this->ask('Email address (leave blank if not available)');
        $phone = $this->option('phone') ?: $this->ask('Phone number (leave blank if not available)');
        $password = $this->option('password') ?: $this->secret('Temporary password');

        if (Role::query()->count() === 0) {
            $this->callSilent('db:seed', [
                '--class' => RolePermissionSeeder::class,
                '--force' => true,
            ]);
        }

        if (! Setting::query()->exists()) {
            $this->callSilent('db:seed', [
                '--class' => SettingsSeeder::class,
                '--force' => true,
            ]);
        }

        $email = $email !== null && trim($email) !== '' ? mb_strtolower(trim($email)) : null;
        $phone = PhoneNumber::normalize($phone);

        if (! $email && ! $phone) {
            $this->error('Provide at least an email address or a phone number.');

            return self::FAILURE;
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:32', 'unique:users,phone'],
            'password' => ['required', 'string', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if (User::withTrashed()->where('email', $email)->exists() || User::withTrashed()->where('phone', $phone)->exists()) {
            $this->error('A user with this email or phone already exists.');

            return self::FAILURE;
        }

        $user = $users->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'is_active' => true,
            'must_change_password' => true,
        ], [RoleName::SystemAdmin->value]);

        $audit->log(
            AuditEvent::AdminBootstrapped,
            $user,
            "Bootstrapped System Admin account for {$user->name}.",
            [],
            ['email' => $user->email, 'phone' => $user->phone],
            $user,
        );

        $this->info('System Admin created successfully.');
        $this->line('  Name:  '.$user->name);
        $this->line('  Login: '.($user->email ?? $user->phone));
        $this->warn('The account must change its temporary password at first sign-in.');

        if (User::role(RoleName::SystemAdmin->value)->where('is_active', true)->count() < 2) {
            $this->warn('Only one active administrator exists. Create a second System Admin before going live.');
        }

        return self::SUCCESS;
    }
}
