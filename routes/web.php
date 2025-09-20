<?php

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

// Public blog routes
Route::get('/', [PageController::class, 'index'])->name('blog.index');
Route::get('/post/{post}', [PageController::class, 'show'])->name('blog.show');

// Authentication routes
Route::get('/login', [PageController::class, 'loginForm'])->name('login');
Route::get('/register', [PageController::class, 'registerForm'])->name('register');


// Protected routes

Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
Route::get('/create', [PageController::class, 'createForm'])->name('blog.create');
Route::get('/posts/{post}/edit', [PageController::class, 'editForm'])->name('blog.edit');



Scramble::registerUiRoute(path: 'docs/api/v1', api: 'v1');
Scramble::registerJsonSpecificationRoute(path: 'docs/api_v1.json', api: 'v1');
