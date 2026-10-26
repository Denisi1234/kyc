<?php

namespace App\Http\Requests;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->filled('email') ? mb_strtolower(trim((string) $this->input('email'))) : null,
            'phone' => PhoneNumber::normalize($this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:32'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'residence' => ['nullable', 'string', 'max:150'],
            'preferred_section' => ['nullable', 'in:soprano,alto,tenor,bass,not_sure'],
            'membership_information' => ['nullable', 'string', 'max:2000'],
            'privacy_agreement' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->filled('email') && ! $this->filled('phone')) {
                $validator->errors()->add('email', 'Provide at least an email address or phone number.');
            }

            if ($this->filled('phone') && ! PhoneNumber::isValid($this->input('phone'))) {
                $validator->errors()->add('phone', 'Enter a valid phone number, for example +255 753 423 251.');
            }
        });
    }
}
