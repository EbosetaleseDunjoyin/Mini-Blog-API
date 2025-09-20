<?php


namespace App\Services;

use App\Models\Post;



class PostService
{
    public function createPost(array $data): Post
    {
        $post = Post::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'user_id' => $data['user_id'],
        ]);

        return $post;
    }

    public function updatePost(Post $post, array $data): Post
    {
        $post->update($data);
        return $post;
    }
    

}