<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(rand(4, 8)),
            'body' => fake()->paragraphs(rand(3, 6), true),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Create a post with a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }


    /**
     * Create a tech-related post.
     */
    public function techPost(): static
    {
        $techTopics = [
            'The Future of Web Development',
            'Building APIs with Laravel Sanctum',
            'Understanding Modern JavaScript',
            'Database Design Best Practices',
            'DevOps for Beginners',
            'Introduction to Docker',
            'Vue.js vs React: A Comparison',
        ];

        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement($techTopics),
            'body' => fake()->paragraphs(rand(4, 7), true),
        ]);
    }
}
