<?php

namespace App\Http\Requests;

use App\Enums\RoleName;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->filled('email') ? mb_strtolower(trim((string) $this->input('email'))) : null,
            'phone' => PhoneNumber::normalize($this->input('phone')),
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
            'must_change_password' => $this->has('must_change_password') ? $this->boolean('must_change_password') : true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'phone' => [
                'nullable',
                'string',
                'max:32',
                'unique:users,phone',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value !== null && ! PhoneNumber::isValid($value)) {
                        $fail('Enter a valid phone number, for example +255 753 423 251.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::in(RoleName::values())],
            'is_active' => ['boolean'],
            'must_change_password' => ['boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->filled('email') && ! $this->filled('phone')) {
                $validator->errors()->add('email', 'Provide at least an email address or a phone number.');
            }
        });
    }
}
