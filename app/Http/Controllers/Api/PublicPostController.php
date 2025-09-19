<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicPostController extends Controller
{
    /**
     * Display a listing of all posts (public access).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::with('user:id,name')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $posts = $query->paginate($perPage);

        return response()->json([
            'posts' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
                'has_more' => $posts->hasMorePages(),
            ]
        ]);
    }

    /**
     * Display the specified post (public access).
     */
    public function show(Post $post): JsonResponse
    {
        return response()->json([
            'post' => $post->load('user:id,name')
        ]);
    }

    /**
     * Search posts by title or body.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        
        if (empty($search)) {
            return response()->json([
                'message' => 'Search query is required',
                'posts' => []
            ], 400);
        }

        $query = Post::with('user:id,name')
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc');

        // Pagination
        $perPage = $request->get('per_page', 10);
        $posts = $query->paginate($perPage);

        return response()->json([
            'search_query' => $search,
            'posts' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
                'has_more' => $posts->hasMorePages(),
            ]
        ]);
    }
}