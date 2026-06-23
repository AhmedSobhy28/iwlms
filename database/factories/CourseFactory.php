<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(3, true),
            'thumbnail' => null,
            'category_id' => Category::factory(),
        ];
    }
}
