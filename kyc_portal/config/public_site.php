<?php

return [
    'navigation' => [
        ['label' => 'Home', 'route' => 'public.home'],
        [
            'label' => 'About',
            'page' => 'about',
            'pages' => ['about', 'history', 'mission', 'leadership'],
        ],
        ['label' => 'Events', 'page' => 'events', 'routes' => ['public.events.*']],
        ['label' => 'Gallery', 'page' => 'gallery'],
        ['label' => 'News', 'page' => 'news'],
        ['label' => 'Contact', 'page' => 'contact'],
    ],

    'pages' => [
        'about' => [
            'title' => 'About KYC',
            'parent' => 'About',
            'eyebrow' => 'Who we are',
            'description' => 'Kijitonyama Youth Choir is a community of young singers serving Kijitonyama Lutheran Church and the wider Dar es Salaam community through sacred choral music.',
        ],
        'history' => [
            'title' => 'Our History',
            'parent' => 'About',
            'eyebrow' => 'Our story',
            'description' => 'The story of Kijitonyama Youth Choir, from a small group of singers to a choir serving churches, schools and communities across Tanzania.',
        ],
        'mission' => [
            'title' => 'Mission & Vision',
            'parent' => 'About',
            'eyebrow' => 'Why we sing',
            'description' => 'Our mission, vision and the values that shape every rehearsal and every performance.',
        ],
        'leadership' => [
            'title' => 'Leadership',
            'parent' => 'About',
            'eyebrow' => 'Our people',
            'description' => 'Meet the Youth Union, choir cabinet and committees serving our singers.',
        ],
        'contact' => [
            'title' => 'Contact Us',
            'parent' => 'About',
            'eyebrow' => 'Reach the choir',
            'description' => 'Send an inquiry, ask about membership or find out where the choir rehearses.',
        ],
        'events' => [
            'title' => 'Upcoming Events',
            'parent' => 'Events & Activities',
            'eyebrow' => 'Dates for your diary',
            'description' => 'Concerts, services, rehearsals and community activities coming up with Kijitonyama Youth Choir.',
        ],
        'news' => [
            'title' => 'News & Announcements',
            'parent' => 'News',
            'eyebrow' => 'Latest from the choir',
            'description' => 'Official announcements, event updates and stories from Kijitonyama Youth Choir.',
        ],
        'gallery' => [
            'title' => 'Photo Gallery',
            'parent' => 'Gallery',
            'eyebrow' => 'Moments in music',
            'description' => 'Photographs from rehearsals, worship services, concerts and community outreach.',
        ],
        'join' => [
            'title' => 'Join the Choir',
            'parent' => 'Get Involved',
            'eyebrow' => 'Become a member',
            'description' => 'Apply to join Kijitonyama Youth Choir. Tell us about yourself and our secretary will be in touch.',
        ],
        'privacy' => [
            'title' => 'Privacy Notice',
            'parent' => 'Legal',
            'eyebrow' => 'Your information',
            'description' => 'How Kijitonyama Youth Choir collects, uses and protects personal information.',
        ],
    ],

    'contact' => [
        'organization' => 'Kijitonyama Youth Choir',
        'church' => 'Kijitonyama Lutheran Church',
        'address' => 'Kijitonyama, Dar es Salaam, Tanzania',
        'phone' => '+255 753 423 251',
        'phone_link' => '+255753423251',
        'email' => 'info@kijitonyamayouthchoir.org',
        'instagram' => 'https://www.instagram.com/kijitonyamayouthchoir/',
        'youtube' => 'https://youtu.be/qcQDuiPLFV4?si=OhcZHHD8c80DFi9p',
        'season_video' => 'https://youtu.be/qcQDuiPLFV4?si=OhcZHHD8c80DFi9p',
        'rehearsal_days' => 'Tuesday & Thursday',
        'rehearsal_time' => '6:00 – 8:30 PM',
    ],
];
