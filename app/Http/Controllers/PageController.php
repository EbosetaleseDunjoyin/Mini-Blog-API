<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PageController extends Controller
{
    /**
     * Display all posts (public page)
     */
    public function index(Request $request)
    {

        return view('blog.index');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * Display a single post (public page)
     */
    public function show(Post $post)
    {
        return view('blog.show');
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
     * Show create post form
     */
    public function createForm()
    {
        return view('blog.create');
    }

    

    /**
     * Show edit post form
     */
    public function editForm(Post $post)
    {
        return view('blog.edit');
    }

   
}
