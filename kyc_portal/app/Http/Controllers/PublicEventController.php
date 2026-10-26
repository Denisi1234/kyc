<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class PublicEventController extends Controller
{
    public function index(): View
    {
        $upcoming = Event::query()
            ->published()
            ->upcoming()
            ->ordered()
            ->get();

        $past = Event::query()
            ->published()
            ->past()
            ->orderByDesc('starts_at')
            ->limit(6)
            ->get();

        return view('public.events.index', [
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }

    public function show(Event $event): View
    {
        abort_unless($event->is_published, 404);

        $related = Event::query()
            ->published()
            ->upcoming()
            ->whereKeyNot($event->getKey())
            ->ordered()
            ->limit(3)
            ->get();

        return view('public.events.show', [
            'event' => $event,
            'related' => $related,
        ]);
    }
}
