<?php

use App\Models\User;
use App\Models\Post;

test('public posts endpoint returns posts', function () {
    // Create a user and post
    $user = User::factory()->create();
    $post = Post::create([
        'title' => 'Test Post',
        'body' => 'This is a test post content.',
        'user_id' => $user->id,
    ]);

    $response = $this->getJson('/api/public/posts');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'posts' => [
                     '*' => ['id', 'title', 'body', 'user_id', 'created_at', 'updated_at', 'user']
                 ],
                 'pagination'
             ]);
});

test('user registration works', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson('/api/auth/register', $userData);

    $response->assertStatus(201)
             ->assertJsonStructure([
                 'message',
                 'user' => ['id', 'name', 'email'],
                 'token',
                 'token_type'
             ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);
});

test('user login works', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $loginData = [
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    $response = $this->postJson('/api/auth/login', $loginData);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'message',
                 'user' => ['id', 'name', 'email'],
                 'token',
                 'token_type'
             ]);
});

test('authenticated user can create post', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $postData = [
        'title' => 'My New Post',
        'body' => 'This is the content of my new post.',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->postJson('/api/posts', $postData);

    $response->assertStatus(201)
             ->assertJsonStructure([
                 'message',
                 'post' => ['id', 'title', 'body', 'user_id', 'created_at', 'updated_at']
             ]);

    $this->assertDatabaseHas('posts', [
        'title' => 'My New Post',
        'body' => 'This is the content of my new post.',
        'user_id' => $user->id,
    ]);
});

test('user can only access own posts', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $post = Post::create([
        'title' => 'User 1 Post',
        'body' => 'This post belongs to user 1.',
        'user_id' => $user1->id,
    ]);

    $token2 = $user2->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token2)
                     ->getJson('/api/posts/' . $post->id);

    $response->assertStatus(403);
});

test('search functionality works', function () {
    $user = User::factory()->create();
    
    Post::create([
        'title' => 'Laravel Tutorial',
        'body' => 'Learn Laravel framework.',
        'user_id' => $user->id,
    ]);

    Post::create([
        'title' => 'PHP Basics',
        'body' => 'Understanding PHP fundamentals.',
        'user_id' => $user->id,
    ]);

    $response = $this->getJson('/api/public/posts/search?q=Laravel');

    $response->assertStatus(200)
             ->assertJsonFragment(['title' => 'Laravel Tutorial']);
});
