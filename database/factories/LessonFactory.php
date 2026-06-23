<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(2, true),
            'order' => fake()->numberBetween(1, 20),
            'course_id' => Course::factory(),
        ];
    }

    public function withVideoUrl(): static
    {
        return $this->state(fn () => [
            'content' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }
}
