<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::query()->value('id');

        $events = [
            [
                'title' => 'Youth Praise Night',
                'slug' => 'youth-praise-night',
                'category' => 'Fellowship',
                'summary' => 'An evening of praise, testimony and congregational song led by the youth choir.',
                'description' => "Join Kijitonyama Youth Choir for a night of praise and worship at Kijitonyama Lutheran Church.\n\nThe evening will bring together singers, instrumentalists and the congregation for a programme of contemporary and traditional sacred music. Doors open at 6:00 PM and the service begins at 6:30 PM.",
                'starts_at' => '2026-11-14 18:30:00',
                'ends_at' => '2026-11-14 20:30:00',
                'venue' => 'Kijitonyama Lutheran Church',
                'address' => 'Kijitonyama, Dar es Salaam',
                'image_path' => 'assets/img/mission.jpeg',
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Advent Carol Service',
                'slug' => 'advent-carol-service',
                'category' => 'Worship',
                'summary' => 'Scripture, carols and candlelight as the choir opens the Advent season.',
                'description' => "Begin the Advent season with the choir in an evening of carols, scripture and congregational song.\n\nThe service includes readings from the Advent story, choral anthems and favourite carols sung by the whole congregation. All are welcome.",
                'starts_at' => '2026-12-05 18:00:00',
                'ends_at' => '2026-12-05 19:30:00',
                'venue' => 'Kijitonyama Lutheran Church',
                'address' => 'Kijitonyama, Dar es Salaam',
                'image_path' => 'assets/img/team-banner.jpg',
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Carry the Song: Christmas Concert',
                'slug' => 'carry-the-song-christmas-concert',
                'category' => 'Concert',
                'summary' => 'The choir’s annual Christmas concert, with guests and a programme for the whole family.',
                'description' => "Celebrate Christmas with Kijitonyama Youth Choir at our annual festive concert.\n\nThe programme features choral favourites, new arrangements and a closing carol sing. Bring family and friends for an evening of music, fellowship and joy.",
                'starts_at' => '2026-12-20 19:00:00',
                'ends_at' => '2026-12-20 21:00:00',
                'venue' => 'Kijitonyama Lutheran Church',
                'address' => 'Kijitonyama, Dar es Salaam',
                'image_path' => 'assets/img/contact.jpeg',
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Community Outreach Concert',
                'slug' => 'community-outreach-concert',
                'category' => 'Community',
                'summary' => 'A community concert supporting school music programmes across Dar es Salaam.',
                'description' => "Thank you to everyone who sang, gave and attended the choir’s outreach concert.\n\nThe evening raised support for school music programmes and introduced young singers to the choir’s work in the community.",
                'starts_at' => '2026-09-27 16:00:00',
                'ends_at' => '2026-09-27 18:00:00',
                'venue' => 'Kijitonyama Lutheran Church',
                'address' => 'Kijitonyama, Dar es Salaam',
                'image_path' => 'assets/img/team/gladness-richard-shija.jpg',
                'is_published' => true,
                'is_featured' => false,
            ],
            [
                'title' => 'Choir Anniversary Service',
                'slug' => 'choir-anniversary-service',
                'category' => 'Worship',
                'summary' => 'A service of thanksgiving for another year of music and ministry.',
                'description' => "The choir marked its anniversary with a service of thanksgiving, joined by alumni, families and friends of the ministry.\n\nWe are grateful for every singer, leader and supporter who has carried the song with us.",
                'starts_at' => '2026-08-16 10:00:00',
                'ends_at' => '2026-08-16 12:00:00',
                'venue' => 'Kijitonyama Lutheran Church',
                'address' => 'Kijitonyama, Dar es Salaam',
                'image_path' => 'assets/img/team-banner.jpg',
                'is_published' => true,
                'is_featured' => false,
            ],
            [
                'title' => 'Epiphany Choral Evensong',
                'slug' => 'epiphany-choral-evensong',
                'category' => 'Worship',
                'summary' => 'A reflective evening service for the season of Epiphany.',
                'description' => "Join the choir for a choral evensong marking the season of Epiphany.\n\nThe service will include psalms, canticles and anthems reflecting on light, revelation and mission.",
                'starts_at' => '2027-01-10 18:00:00',
                'ends_at' => '2027-01-10 19:15:00',
                'venue' => 'Kijitonyama Lutheran Church',
                'address' => 'Kijitonyama, Dar es Salaam',
                'image_path' => 'assets/img/mission.jpeg',
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Choir Retreat and Rehearsal Weekend',
                'slug' => 'choir-retreat-rehearsal-weekend',
                'category' => 'Rehearsal',
                'summary' => 'A weekend of rehearsal, planning and fellowship for the new season.',
                'description' => "A two-day retreat for choir members: repertoire rehearsal, sectional work, planning and fellowship.\n\nDetails and registration will be shared with members through the portal.",
                'starts_at' => '2027-02-20 09:00:00',
                'ends_at' => '2027-02-21 16:00:00',
                'venue' => 'Kijitonyama Lutheran Church',
                'address' => 'Kijitonyama, Dar es Salaam',
                'image_path' => 'assets/img/team-banner.jpg',
                'is_published' => false,
                'is_featured' => false,
            ],
        ];

        foreach ($events as $event) {
            Event::query()->updateOrCreate(
                ['slug' => $event['slug']],
                $event + ['created_by' => $authorId, 'updated_by' => $authorId],
            );
        }
    }
}
