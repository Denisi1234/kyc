<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        $starts = fake()->dateTimeBetween('+1 day', '+3 months');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 9999),
            'category' => fake()->randomElement(['Concert', 'Worship', 'Rehearsal', 'Community']),
            'summary' => fake()->sentence(16),
            'description' => fake()->paragraphs(3, true),
            'starts_at' => $starts,
            'ends_at' => (clone $starts)->modify('+2 hours'),
            'venue' => 'Kijitonyama Lutheran Church',
            'address' => 'Kijitonyama, Dar es Salaam',
            'image_path' => null,
            'is_published' => true,
            'is_featured' => false,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }

    public function past(): static
    {
        return $this->state(fn () => [
            'starts_at' => fake()->dateTimeBetween('-6 months', '-1 day'),
            'ends_at' => null,
        ]);
    }
}
