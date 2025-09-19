<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users with specific credentials for easy testing
        $john = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $jane = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Create additional random users
        $randomUsers = User::factory(3)->create();

        // Create specific posts for John
        Post::factory()->welcomePost()->forUser($john)->create();
        Post::factory()->aboutLaravel()->forUser($john)->create();
        
        // Create tech posts for Jane
        Post::factory()->techPost()->forUser($jane)->create([
            'title' => 'The Future of Web Development',
            'body' => "Web development is constantly evolving, and it's exciting to see where the industry is heading.\n\nSome trends I'm particularly excited about:\n\n1. Progressive Web Apps (PWAs)\n2. Server-side rendering with modern frameworks\n3. Improved developer experience with better tooling\n4. Enhanced performance optimization techniques\n5. Better accessibility standards\n\nWhat trends are you most excited about? Let me know in the comments!",
        ]);

        Post::factory()->techPost()->forUser($jane)->create([
            'title' => 'Building APIs with Laravel Sanctum',
            'body' => "Today I implemented API authentication using Laravel Sanctum, and I'm impressed with how straightforward it is.\n\nSanctum provides a lightweight authentication system for SPAs and mobile applications. Here's what I love about it:\n\n- Simple token-based authentication\n- Built-in CSRF protection for SPAs\n- Easy to set up and configure\n- Integrates seamlessly with Laravel\n\nIf you're building an API with Laravel, definitely consider using Sanctum for authentication.",
        ]);

        // Create random posts for all users
        $allUsers = collect([$john, $jane])->concat($randomUsers);
        
        $allUsers->each(function ($user) {
            // Create 2-4 random posts for each user
            Post::factory(rand(2, 4))->forUser($user)->create();
        });

        // Create some additional tech posts with random users
        Post::factory(5)->techPost()->create();
    }
}
