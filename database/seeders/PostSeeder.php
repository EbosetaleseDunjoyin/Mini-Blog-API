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

        $john = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $jane = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Create users
        $randomUsers = User::factory(3)->create();

        $allUsers = collect([$john, $jane])->concat($randomUsers);
        
        $allUsers->each(function ($user) {
            Post::factory(rand(2, 4))->forUser($user)->techPost()->create();
        });

    }
}
