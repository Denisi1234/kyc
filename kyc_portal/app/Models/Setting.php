<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'is_encrypted',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
        ];
    }

    public function typedValue(): mixed
    {
        $raw = $this->value;

        if ($raw === null) {
            return null;
        }

        if ($this->is_encrypted) {
            $raw = Crypt::decryptString($raw);
        }

        return match ($this->type) {
            'boolean' => (bool) $raw,
            'integer' => (int) $raw,
            'json' => json_decode($raw, true),
            default => $raw,
        };
    }

    public function assignTypedValue(mixed $value): void
    {
        $raw = match ($this->type) {
            'boolean' => $value ? '1' : '0',
            'integer' => (string) (int) $value,
            'json' => json_encode($value),
            default => $value === null ? null : (string) $value,
        };

        if ($this->is_encrypted && $raw !== null) {
            $raw = Crypt::encryptString($raw);
        }

        $this->value = $raw;
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting?->typedValue() ?? $default;
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
