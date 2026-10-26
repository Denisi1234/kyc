<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $input = trim($value);
        $digits = preg_replace('/\D+/', '', $input) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '255')) {
            $digits = substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) === 9 && preg_match('/^[67]\d{8}$/', $digits)) {
            return '+255'.$digits;
        }

        if (str_starts_with($input, '+')) {
            return '+'.$digits;
        }

        if (strlen($digits) >= 7 && strlen($digits) <= 15) {
            return '+'.$digits;
        }

        return null;
    }

    public static function isValid(?string $value): bool
    {
        return is_string($value) && preg_match('/^\+[1-9]\d{7,14}$/', $value) === 1;
    }
}
