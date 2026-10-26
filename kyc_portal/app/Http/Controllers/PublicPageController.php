<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'events' => Event::query()
                ->published()
                ->upcoming()
                ->ordered()
                ->take(3)
                ->get(),
            'news' => $this->news(),
            'leaders' => $this->leaders(),
            'gallery' => $this->gallery(),
        ]);
    }

    public function page(string $page): View
    {
        $pages = config('public_site.pages', []);

        abort_unless(array_key_exists($page, $pages), 404);

        $data = [
            'slug' => $page,
            'page' => $pages[$page],
        ];

        if ($page === 'gallery') {
            $data['gallery'] = $this->gallery();
        }

        if ($page === 'news') {
            $data['articles'] = $this->news();
        }

        if (view()->exists("public.{$page}")) {
            return view("public.{$page}", $data);
        }

        return view('public.page', $data);
    }

    private function news(): array
    {
        return [
            [
                'title' => 'Our Advent and Christmas Season of Song Confirmed',
                'category' => 'Concerts & Events',
                'date' => 'Oct 18, 2026',
                'read_time' => '4 min read',
                'excerpt' => 'Rehearsal dates, carol services, and the choir’s annual Christmas concert programme are now officially confirmed. Read details on schedule, venues, and musical repertoire.',
                'image' => 'assets/img/team-banner.jpg',
                'author' => 'Choir Cabinet',
                'featured' => true,
            ],
            [
                'title' => 'Welcoming New Voices: Open Rehearsals at Kijitonyama',
                'category' => 'Membership',
                'date' => 'Oct 10, 2026',
                'read_time' => '3 min read',
                'excerpt' => 'If you have been thinking about singing sacred choral music, join us on Tuesday or Thursday. Open voice coaching and section introductions are ongoing.',
                'image' => 'assets/img/team/gladness-richard-shija.jpg',
                'author' => 'Music Committee',
                'featured' => false,
            ],
            [
                'title' => 'Outreach Concert Raises Support for Local School Programs',
                'category' => 'Community Service',
                'date' => 'Sep 28, 2026',
                'read_time' => '5 min read',
                'excerpt' => 'Thank you to all who attended and contributed. KYC raised vital resources to support primary and secondary school music initiatives across Dar es Salaam.',
                'image' => 'assets/img/mission.jpeg',
                'author' => 'Social Committee',
                'featured' => false,
            ],
            [
                'title' => 'Repertoire & Sight-Reading Clinic Notes for Singers',
                'category' => 'Music Education',
                'date' => 'Sep 12, 2026',
                'read_time' => '4 min read',
                'excerpt' => 'Section leaders have published the rehearsal guide and classical anthem scores for the upcoming quarter. Choir members can download notes or review at practice.',
                'image' => 'assets/img/team/emmanuel-yohana.jpg',
                'author' => 'Emmanuel Yohana',
                'featured' => false,
            ],
            [
                'title' => 'Mid-Year Fellowship and Thanksgiving Sunday Highlights',
                'category' => 'Fellowship',
                'date' => 'Aug 24, 2026',
                'read_time' => '3 min read',
                'excerpt' => 'A look back at our community fellowship Sunday filled with testimony, laughter, and collective songs of gratitude at Kijitonyama Lutheran Church.',
                'image' => 'assets/img/contact.jpeg',
                'author' => 'Youth Leadership',
                'featured' => false,
            ],
        ];
    }

    private function leaders(): array
    {
        return [
            [
                'name' => 'Jenipher Daudi',
                'role' => 'Choir Chairperson',
                'initials' => 'JD',
                'photo' => null,
                'note' => 'Leads the choir cabinet and works with the Youth Union to guide the choir’s programme.',
            ],
            [
                'name' => 'Fredrick Hosea',
                'role' => 'Choir Secretary',
                'initials' => 'FH',
                'photo' => null,
                'note' => 'Keeps membership records, meeting minutes and the choir’s day-to-day administration in order.',
            ],
            [
                'name' => 'Emmanuel Yohana',
                'role' => 'Music Committee',
                'initials' => 'EY',
                'photo' => 'assets/img/team/emmanuel-yohana.jpg',
                'note' => 'Guides repertoire, sectional preparation and the musical standard of every rehearsal.',
            ],
        ];
    }

    private function gallery(): array
    {
        return [
            ['image' => 'assets/img/team-banner.jpg', 'title' => 'Choir Full Ensemble', 'category' => 'Choir'],
            ['image' => 'assets/img/mission.jpeg', 'title' => 'Praise & Sacred Worship', 'category' => 'Worship'],
            ['image' => 'assets/img/contact.jpeg', 'title' => 'Youth Choir at Rehearsal', 'category' => 'Rehearsal'],
            ['image' => 'assets/img/team/angel-thobias-mwathalonga.jpg', 'title' => 'Angel Thobias Mwathalonga', 'category' => 'Singers'],
            ['image' => 'assets/img/team/gladness-richard-shija.jpg', 'title' => 'Gladness Richard Shija', 'category' => 'Singers'],
            ['image' => 'assets/img/team/emmanuel-yohana.jpg', 'title' => 'Emmanuel Yohana', 'category' => 'Music Committee'],
            ['image' => 'assets/img/team/collins-nicholas-francis.jpg', 'title' => 'Collins Nicholas Francis', 'category' => 'Singers'],
            ['image' => 'assets/img/team/nicholaus-paul-mussa.jpg', 'title' => 'Nicholaus Paul Mussa', 'category' => 'Singers'],
            ['image' => 'assets/img/team/linah-godlisten-temu.jpg', 'title' => 'Linah Godlisten Temu', 'category' => 'Singers'],
            ['image' => 'assets/img/team/moreen-filberth-shija.jpg', 'title' => 'Moreen Filberth Shija', 'category' => 'Singers'],
            ['image' => 'assets/img/team/happpyness-atilio-nzumile.jpg', 'title' => 'Happyness Atilio Nzumile', 'category' => 'Singers'],
            ['image' => 'assets/img/team/zackaria-gasper-mleleu.jpg', 'title' => 'Zackaria Gasper Mleleu', 'category' => 'Singers'],
            ['image' => 'assets/img/team/eliah-ruvugo-minister.jpg', 'title' => 'Eliah Ruvugo Minister', 'category' => 'Leadership'],
        ];
    }
}
