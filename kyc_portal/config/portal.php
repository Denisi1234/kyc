<?php

return [

    'settings' => [

        'organization' => [
            'label' => 'Organisation',
            'description' => 'Public identity and contact details used across the portal.',
            'fields' => [
                'organization.name' => [
                    'label' => 'Choir name',
                    'type' => 'text',
                    'rules' => 'required|string|max:120',
                    'default' => 'Kijitonyama Youth Choir',
                ],
                'organization.short_name' => [
                    'label' => 'Short name',
                    'type' => 'text',
                    'rules' => 'required|string|max:20',
                    'default' => 'KYC',
                ],
                'organization.email' => [
                    'label' => 'Contact email',
                    'type' => 'email',
                    'rules' => 'nullable|email|max:150',
                    'default' => null,
                ],
                'organization.phone' => [
                    'label' => 'Contact phone',
                    'type' => 'tel',
                    'rules' => 'nullable|string|max:30',
                    'default' => '+255 753 423 251',
                ],
                'organization.address' => [
                    'label' => 'Address',
                    'type' => 'textarea',
                    'rules' => 'nullable|string|max:300',
                    'default' => 'Kijitonyama Lutheran Church, Dar es Salaam',
                ],
            ],
        ],

        'finance' => [
            'label' => 'Finance',
            'description' => 'Currency and receipt defaults for contribution records.',
            'fields' => [
                'finance.currency' => [
                    'label' => 'Currency',
                    'type' => 'text',
                    'rules' => 'required|string|size:3',
                    'default' => 'TZS',
                ],
                'finance.receipt_prefix' => [
                    'label' => 'Receipt prefix',
                    'type' => 'text',
                    'rules' => 'required|string|max:10|alpha_num',
                    'default' => 'KYC',
                ],
                'finance.claim_requires_proof' => [
                    'label' => 'Require proof for member claims',
                    'type' => 'boolean',
                    'rules' => 'boolean',
                    'default' => true,
                ],
            ],
        ],

        'attendance' => [
            'label' => 'Attendance',
            'description' => 'Defaults used when recording rehearsal and event attendance.',
            'fields' => [
                'attendance.late_after_minutes' => [
                    'label' => 'Late after (minutes)',
                    'type' => 'integer',
                    'rules' => 'required|integer|min:0|max:120',
                    'default' => 15,
                ],
            ],
        ],

        'security' => [
            'label' => 'Security',
            'description' => 'Account and access policy for the portal.',
            'fields' => [
                'security.min_admins' => [
                    'label' => 'Minimum active administrators',
                    'type' => 'integer',
                    'rules' => 'required|integer|min:1|max:10',
                    'default' => 2,
                ],
                'security.session_timeout_minutes' => [
                    'label' => 'Session timeout (minutes)',
                    'type' => 'integer',
                    'rules' => 'required|integer|min:15|max:1440',
                    'default' => 120,
                ],
            ],
        ],

        'locale' => [
            'label' => 'Language',
            'description' => 'Default language for portal screens. Swahili translation follows the public site.',
            'fields' => [
                'locale.default' => [
                    'label' => 'Default language',
                    'type' => 'select',
                    'rules' => 'required|in:en,sw',
                    'default' => 'en',
                    'options' => [
                        'en' => 'English',
                        'sw' => 'Kiswahili',
                    ],
                ],
            ],
        ],

    ],

];
