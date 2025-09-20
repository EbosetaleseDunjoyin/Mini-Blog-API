<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PostService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use ApiResponder, AuthorizesRequests;

    public function __construct(
         private PostService $postService
    )
    {
    }

     /**
     * Display all posts (public access).
     * @unauthenticated
     * @param Request $request
     * @response array{
     *  "status": boolean,
     *  "message": "Posts retrieved successfully",
     *   "data": Post[]
     * }
     */
    public function getPosts(Request $request): JsonResponse
    {
        $posts = Post::with('user:id,name')->orderBy('created_at', 'desc')
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('body', 'like', "%{$search}%");
                });
            })->paginate(10);


        return $this->successResponse("Posts retrieved successfully", $posts);
    }

     /**
     * Display single post.
     * 
     * @param Request $request
     * @param Post $post
     * @response array{
     *  "status": boolean,
     *  "message": "Post retrieved successfully",
     *   "data": Post
     * }
     * @return JsonResponse
     */
    public function showPost(Request $request, Post $post): JsonResponse
    {
        $post->load('user:id,name');
        return $this->successResponse("Post retrieved successfully", $post);
    }

    /**
     * Display the user's posts.
     * @param Request $request
     * @response array{
     *  "status": boolean,
     *  "message": "User posts retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Post Title",
     *       "body": "Post Body",
     *       "user_id": 1,
     *       "created_at": "2023-01-01T00:00:00Z",
     *       "updated_at": "2023-01-01T00:00:00Z"
     *     }
     *   ]
     * }
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $posts = $request->user()->posts()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $this->successResponse("User posts retrieved successfully", $posts);
    }

    /**
     * Create a post.
     * @param Request $request
     * @response array{
     *  "status": boolean,
     *  "message": "Post created successfully",
     *   "data": {
     *       "id": 1,
     *       "title": "Post Title",
     *       "body": "Post Body",
     *       "user_id": 1,
     *       "created_at": "2023-01-01T00:00:00Z",
     *       "updated_at": "2023-01-01T00:00:00Z"
     *     }
     * }
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        try {
           DB::beginTransaction();
           $data = $request->only(['title', 'body']);
           $data['user_id'] = $request->user()->id;
           $post = $this->postService->createPost( $data);
           DB::commit();
           return $this->successResponse("Post created successfully", $post, 201);
       } catch (Exception $e) {
           DB::rollBack();
           Log::error('Post creation error: ' . $e->getMessage());
           return $this->errorResponse("Failed to create post", 500);
       }

        
    }

    /**
     * Display the specified post.
     * 
     * @param Request $request
     * @param Post $post
     * @response array{
     *  "status": boolean,
     *  "message": "Post retrieved successfully",
     *   "data": Post
     * }
     * @return JsonResponse
     */
    public function show(Request $request, Post $post): JsonResponse
    {
        $this->authorize('view', $post);
        $post->load('user');
        return $this->successResponse("Post retrieved successfully", $post);
    }

    /**
     * Update a post.
     * @param Request $request
     * @param Post $post
     * @response array{
     *  "status": boolean,
     *  "message": "Post updated successfully",
     *   "data": Post
     * }
     * @return JsonResponse
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);
        
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
        ]);
       try {
           DB::beginTransaction();
           $data = $request->only(['title', 'body']);
           $updatedPost = $this->postService->updatePost($post, $data);
           DB::commit();
           return $this->successResponse('Post updated successfully', $updatedPost);
       } catch (Exception $e) {
           DB::rollBack();
           Log::error('Post update error: ' . $e->getMessage());
           return $this->errorResponse('Failed to update post', 500);
       }
    }

    /**
     * Delete the specified post.
     * @param Post $post
     * @response array{
     *  "status": boolean,
     *  "message": "Post deleted successfully",
     *   "data": []
     * }
     * @return JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);
        
        $post->delete();

        return $this->successResponse('Post deleted successfully', []);
    }
}