<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('portal.settings', []) as $group) {
            foreach ($group['fields'] as $key => $field) {
                $setting = Setting::query()->firstOrNew(['key' => $key]);

                if (! $setting->exists) {
                    $setting->group = $group['label'];
                    $setting->type = $field['type'] ?? 'string';
                    $setting->is_encrypted = (bool) ($field['encrypted'] ?? false);
                    $setting->assignTypedValue($field['default'] ?? null);
                    $setting->save();
                }
            }
        }
    }
}
