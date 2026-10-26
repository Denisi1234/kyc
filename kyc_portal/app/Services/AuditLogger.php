<?php

namespace App\Services;

use App\Enums\AuditEvent;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLogger
{
    /**
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    public function log(
        AuditEvent|string $event,
        ?Model $subject,
        string $description,
        array $oldValues = [],
        array $newValues = [],
        ?User $actor = null,
    ): AuditLog {
        $actor ??= auth()->user();
        $eventValue = $event instanceof AuditEvent ? $event->value : $event;

        return AuditLog::create([
            'actor_id' => $actor?->getKey(),
            'actor_name' => $actor?->name ?? 'System',
            'event' => $eventValue,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'old_values' => $this->redact($oldValues),
            'new_values' => $this->redact($newValues),
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 1000),
        ]);
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function redact(array $values): array
    {
        $sensitive = [
            'password',
            'password_confirmation',
            'current_password',
            'remember_token',
            'token',
            'secret',
            'api_key',
        ];

        return collect($values)
            ->map(function (mixed $value, string $key) use ($sensitive) {
                if (in_array($key, $sensitive, true) || Str::contains($key, ['password', 'secret', 'token'])) {
                    return '[redacted]';
                }

                if (is_array($value)) {
                    return $this->redact($value);
                }

                return $value;
            })
            ->all();
    }
}
