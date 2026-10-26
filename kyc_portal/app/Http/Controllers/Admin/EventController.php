<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AuditEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->string('search')),
            'status' => (string) $request->string('status'),
            'visibility' => (string) $request->string('visibility'),
        ];

        $events = Event::query()
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $search = '%'.$filters['search'].'%';
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', $search)
                        ->orWhere('summary', 'like', $search)
                        ->orWhere('venue', 'like', $search);
                });
            })
            ->when($filters['status'] === 'upcoming', fn ($query) => $query->upcoming())
            ->when($filters['status'] === 'past', fn ($query) => $query->past())
            ->when($filters['visibility'] === 'published', fn ($query) => $query->where('is_published', true))
            ->when($filters['visibility'] === 'draft', fn ($query) => $query->where('is_published', false))
            ->when(
                $filters['status'] === 'past',
                fn ($query) => $query->orderByDesc('starts_at'),
                fn ($query) => $query->orderBy('starts_at'),
            )
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', [
            'events' => $events,
            'filters' => $filters,
            'stats' => [
                'total' => Event::query()->count(),
                'upcoming' => Event::query()->upcoming()->count(),
                'published' => Event::query()->where('is_published', true)->count(),
                'drafts' => Event::query()->where('is_published', false)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.events.create');
    }

    public function store(EventRequest $request, AuditLogger $audit): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $event = Event::query()->create($data);

        $audit->log(
            AuditEvent::EventCreated,
            $event,
            "Created event \"{$event->title}\".",
            [],
            $this->auditValues($event),
        );

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('status', 'Event created.');
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
        ]);
    }

    public function update(EventRequest $request, Event $event, AuditLogger $audit): RedirectResponse
    {
        $oldValues = $this->auditValues($event);
        $oldImage = $event->image_path;

        $data = $this->validatedData($request);
        $data['updated_by'] = $request->user()->id;

        $event->update($data);

        if ($oldImage && $oldImage !== $event->image_path) {
            $this->deleteStoredImage($oldImage);
        }

        $audit->log(
            AuditEvent::EventUpdated,
            $event,
            "Updated event \"{$event->title}\".",
            $oldValues,
            $this->auditValues($event->fresh() ?? $event),
        );

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('status', 'Event updated.');
    }

    public function publish(Event $event, AuditLogger $audit): RedirectResponse
    {
        $event->update(['is_published' => true, 'updated_by' => auth()->id()]);

        $audit->log(
            AuditEvent::EventPublished,
            $event,
            "Published event \"{$event->title}\".",
            ['is_published' => false],
            ['is_published' => true],
        );

        return back()->with('status', 'Event published.');
    }

    public function unpublish(Event $event, AuditLogger $audit): RedirectResponse
    {
        $event->update(['is_published' => false, 'updated_by' => auth()->id()]);

        $audit->log(
            AuditEvent::EventUnpublished,
            $event,
            "Unpublished event \"{$event->title}\".",
            ['is_published' => true],
            ['is_published' => false],
        );

        return back()->with('status', 'Event moved back to draft.');
    }

    public function destroy(Event $event, AuditLogger $audit): RedirectResponse
    {
        $audit->log(
            AuditEvent::EventDeleted,
            $event,
            "Deleted event \"{$event->title}\".",
            $this->auditValues($event),
            [],
        );

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('status', 'Event deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(EventRequest $request): array
    {
        $data = $request->validated();

        unset($data['image']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('events', 'public');
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function auditValues(Event $event): array
    {
        return [
            'title' => $event->title,
            'slug' => $event->slug,
            'category' => $event->category,
            'starts_at' => $event->starts_at?->toDateTimeString(),
            'ends_at' => $event->ends_at?->toDateTimeString(),
            'venue' => $event->venue,
            'is_published' => $event->is_published,
            'is_featured' => $event->is_featured,
        ];
    }

    private function deleteStoredImage(?string $path): void
    {
        if (! $path || str_starts_with($path, 'assets/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
