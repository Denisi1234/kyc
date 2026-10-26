<?php

namespace App\Services;

use App\Enums\AuditEvent;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SettingService
{
    /**
     * @param  array<string, mixed>  $values
     */
    public function update(array $values, ?User $actor = null): void
    {
        DB::transaction(function () use ($values, $actor) {
            $old = [];
            $new = [];

            foreach (config('portal.settings', []) as $group) {
                foreach ($group['fields'] as $key => $field) {
                    $setting = Setting::query()->firstOrCreate(
                        ['key' => $key],
                        [
                            'group' => $group['label'],
                            'type' => $field['type'] ?? 'string',
                            'is_encrypted' => (bool) ($field['encrypted'] ?? false),
                        ],
                    );

                    $setting->fill([
                        'group' => $group['label'],
                        'type' => $field['type'] ?? 'string',
                        'is_encrypted' => (bool) ($field['encrypted'] ?? false),
                    ]);

                    if (($field['type'] ?? 'string') === 'boolean') {
                        $incoming = (bool) ($values[$key] ?? false);
                    } else {
                        if (! array_key_exists($key, $values)) {
                            $setting->save();

                            continue;
                        }

                        $incoming = $values[$key];
                    }

                    if ($setting->is_encrypted && ($incoming === null || $incoming === '')) {
                        $setting->save();

                        continue;
                    }

                    $before = $setting->exists ? $setting->typedValue() : null;
                    $setting->assignTypedValue($incoming);
                    $after = $setting->typedValue();

                    if ($before !== $after) {
                        $old[$key] = $setting->is_encrypted ? '[redacted]' : $before;
                        $new[$key] = $setting->is_encrypted ? '[redacted]' : $after;
                    }

                    $setting->updated_by = $actor?->getKey();
                    $setting->save();
                }
            }

            if ($old !== []) {
                app(AuditLogger::class)->log(
                    AuditEvent::SettingsUpdated,
                    null,
                    'Updated portal settings.',
                    $old,
                    $new,
                    $actor,
                );
            }
        });
    }
}
