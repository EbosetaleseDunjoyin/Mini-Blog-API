<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display all posts (public page)
     */
    public function index(Request $request)
    {
        $query = Post::with('user')->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(10);

        return view('blog.index', compact('posts'));
    }

    /**
     * Display a single post (public page)
     */
    public function show(Post $post)
    {
        $post->load('user');
        return view('blog.show', compact('post'));
    }

    /**
     * Show login form
     */
    public function loginForm()
    {
        return view('auth.login');
    }

    /**
     * Show registration form
     */
    public function registerForm()
    {
        return view('auth.register');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Show user dashboard
     */
    public function dashboard()
    {
        $posts = Auth::user()->posts()->orderBy('created_at', 'desc')->get();
        return view('dashboard', compact('posts'));
    }

    /**
     * Show create post form
     */
    public function createForm()
    {
        return view('blog.create');
    }

    /**
     * Store a new post
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Auth::user()->posts()->create([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect('/dashboard')->with('success', 'Post created successfully!');
    }

    /**
     * Show edit post form
     */
    public function editForm(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        return view('blog.edit', compact('post'));
    }

    /**
     * Update a post
     */
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect('/dashboard')->with('success', 'Post updated successfully!');
    }

    /**
     * Delete a post
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->delete();

        return redirect('/dashboard')->with('success', 'Post deleted successfully!');
    }
}
