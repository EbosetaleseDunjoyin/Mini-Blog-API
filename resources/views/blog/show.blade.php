@extends('layouts.app')

@section('title', $post->title . ' - Mini Blog')

@section('content')
    <article class="card">
        <h1 class="post-title">{{ $post->title }}</h1>
        <div class="post-meta">
            By {{ $post->user->name }} • {{ $post->created_at->format('M j, Y \a\t g:i A') }}
        </div>
        <div class="post-body">{{ $post->body }}</div>
    </article>

    <div style="margin-top: 2rem;">
        <a href="{{ route('blog.index') }}" class="btn btn-secondary">← Back to All Posts</a>
    </div>
@endsection