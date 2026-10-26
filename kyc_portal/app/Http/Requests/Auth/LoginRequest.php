<?php

namespace App\Http\Requests\Auth;

use App\Enums\AuditEvent;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\PhoneNumber;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim((string) $this->input('identifier'));
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $value = $field === 'email' ? mb_strtolower($identifier) : PhoneNumber::normalize($identifier);

        if ($value === null || $value === '') {
            RateLimiter::hit($this->throttleKey(), 60);
            $this->recordFailure($identifier);

            throw ValidationException::withMessages([
                'identifier' => 'These credentials do not match our records, or the account is inactive.',
            ]);
        }

        $credentials = [
            $field => $value,
            'password' => (string) $this->input('password'),
            'is_active' => true,
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), 60);
            $this->recordFailure($identifier);

            throw ValidationException::withMessages([
                'identifier' => 'These credentials do not match our records, or the account is inactive.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        $user = $this->user();
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $this->ip(),
        ])->save();

        app(AuditLogger::class)->log(
            AuditEvent::AuthLogin,
            $user,
            "{$user->name} signed in.",
            [],
            ['identifier' => $identifier],
            $user,
        );
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower((string) $this->input('identifier')).'|'.$this->ip());
    }

    private function recordFailure(string $identifier): void
    {
        $user = User::query()
            ->where('email', mb_strtolower($identifier))
            ->orWhere('phone', PhoneNumber::normalize($identifier))
            ->first();

        app(AuditLogger::class)->log(
            AuditEvent::AuthLoginFailed,
            $user,
            "Failed sign-in attempt for {$identifier}.",
            [],
            ['identifier' => $identifier],
            null,
        );
    }
}
