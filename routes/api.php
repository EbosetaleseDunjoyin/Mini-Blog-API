<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PublicPostController;



// Public routes
Route::prefix('public')->group(function () {
    Route::get('/posts', [PostController::class, 'getPosts']);
    Route::get('/posts/{post}', [PostController::class, 'showPost']);
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected auth routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// All Protected routes
Route::middleware('auth:api')->group(function () {
    
    // Post management routes
    Route::apiResource('posts', PostController::class)->except(['getPosts', 'showPost']);
});

