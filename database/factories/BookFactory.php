<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'subtitle' => fake()->boolean(50) ? fake()->sentence(5) : null,
            'description' => fake()->paragraphs(3, true),
            'cover_url' => fake()->imageUrl(300, 450, 'books', true, 'Faker'),
            'isbn10' => fake()->isbn10(),
            'isbn13' => fake()->isbn13(),
            'page_count' => fake()->numberBetween(100, 1000),
            'published_date' => fake()->date(),
            'language' => fake()->languageCode(),
            'avg_rating' => fake()->randomFloat(2, 1, 5),
            'ratings_count' => fake()->numberBetween(0, 1000),
        ];
    }
}
