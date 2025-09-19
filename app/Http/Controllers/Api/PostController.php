<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the user's posts.
     */
    public function index(Request $request): JsonResponse
    {
        $posts = $request->user()->posts()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'posts' => $posts
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $post = $request->user()->posts()->create([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post->load('user')
        ], 201);
    }

    /**
     * Display the specified post.
     */
    public function show(Request $request, Post $post): JsonResponse
    {
        // Check if the post belongs to the authenticated user
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'post' => $post->load('user')
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        // Check if the post belongs to the authenticated user
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $post->update($request->only(['title', 'body']));

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post->load('user')
        ]);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        // Check if the post belongs to the authenticated user
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}