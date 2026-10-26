<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'full_name',
        'email',
        'phone',
        'date_of_birth',
        'residence',
        'preferred_section',
        'membership_information',
        'status',
        'privacy_agreed_at',
        'submitted_ip',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'privacy_agreed_at' => 'datetime',
        ];
    }
}
