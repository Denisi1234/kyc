<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'category',
        'summary',
        'description',
        'starts_at',
        'ends_at',
        'venue',
        'address',
        'image_path',
        'is_published',
        'is_featured',
        'created_by',
        'updated_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): void
    {
        $query->where('starts_at', '>=', now()->startOfDay());
    }

    public function scopePast(Builder $query): void
    {
        $query->where('starts_at', '<', now()->startOfDay());
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('starts_at');
    }

    public function isPast(): bool
    {
        return $this->starts_at->isBefore(now()->startOfDay());
    }

    public function isUpcoming(): bool
    {
        return ! $this->isPast();
    }

    public function googleCalendarUrl(): string
    {
        $end = $this->ends_at ?? $this->starts_at->copy()->addHours(2);

        return 'https://www.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => $this->title,
            'dates' => $this->starts_at->format('Ymd\THis').'/'.$end->format('Ymd\THis'),
            'ctz' => 'Africa/Dar_es_Salaam',
            'details' => $this->summary,
            'location' => trim(implode(', ', array_filter([$this->venue, $this->address]))),
        ]);
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image_path) {
            return asset('assets/img/team-banner.jpg');
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        if (Str::startsWith($this->image_path, 'assets/')) {
            return asset($this->image_path);
        }

        return asset('storage/'.$this->image_path);
    }
}
