<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

// Public blog routes
Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/post/{post}', [BlogController::class, 'show'])->name('blog.show');

// Authentication routes
Route::get('/login', [BlogController::class, 'loginForm'])->name('login');
Route::post('/login', [BlogController::class, 'login']);
Route::get('/register', [BlogController::class, 'registerForm'])->name('register');
Route::post('/register', [BlogController::class, 'register']);
Route::post('/logout', [BlogController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BlogController::class, 'dashboard'])->name('dashboard');
    Route::get('/create', [BlogController::class, 'createForm'])->name('blog.create');
    Route::post('/posts', [BlogController::class, 'store'])->name('blog.store');
    Route::get('/posts/{post}/edit', [BlogController::class, 'editForm'])->name('blog.edit');
    Route::put('/posts/{post}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('/posts/{post}', [BlogController::class, 'destroy'])->name('blog.destroy');
});
