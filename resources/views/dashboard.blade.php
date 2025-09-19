@extends('layouts.app')

@section('title', 'Dashboard - Mini Blog')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Your Posts</h1>
        <a href="{{ route('blog.create') }}" class="btn">Write New Post</a>
    </div>

    @if($posts->count() > 0)
        @foreach($posts as $post)
            <div class="card">
                <h2 class="post-title">{{ $post->title }}</h2>
                <div class="post-meta">
                    Created {{ $post->created_at->format('M j, Y \a\t g:i A') }}
                    @if($post->updated_at != $post->created_at)
                        • Updated {{ $post->updated_at->format('M j, Y \a\t g:i A') }}
                    @endif
                </div>
                <div class="post-body">
                    {{ Str::limit($post->body, 200) }}
                </div>
                <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('blog.show', $post) }}" class="btn btn-secondary">View</a>
                    <a href="{{ route('blog.edit', $post) }}" class="btn">Edit</a>
                    <form action="{{ route('blog.destroy', $post) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    @else
        <div class="card">
            <p>You haven't written any posts yet.</p>
            <a href="{{ route('blog.create') }}" class="btn" style="margin-top: 1rem;">Write Your First Post</a>
        </div>
    @endif
@endsection