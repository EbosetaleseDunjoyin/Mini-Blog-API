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
    public function forUser(\App\Models\User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a post about Laravel.
     */
    public function aboutLaravel(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Learning Laravel: ' . fake()->sentence(3),
            'body' => "Laravel has been an incredible journey for me. The framework provides so many powerful features out of the box, including:\n\n- Eloquent ORM for database interactions\n- Blade templating engine\n- Artisan command line tool\n- Built-in authentication\n- And much more!\n\n" . fake()->paragraphs(rand(2, 4), true),
        ]);
    }

    /**
     * Create a welcome post.
     */
    public function welcomePost(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Welcome to My Blog',
            'body' => "Hello everyone! Welcome to my new blog. I'm excited to share my thoughts and experiences with you all.\n\nThis is my first post, and I hope you'll find the content interesting and engaging. I plan to write about technology, life experiences, and various topics that interest me.\n\nStay tuned for more posts!",
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
