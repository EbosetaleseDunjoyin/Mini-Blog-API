@extends('layouts.app')

@section('title', 'All Posts - Mini Blog')

@section('content')
    <div class="search-form">
        <form action="{{ route('blog.index') }}" method="GET" style="display: flex; width: 100%;">
            <input type="text" name="search" placeholder="Search posts..." value="{{ request('search') }}">
            <button type="submit" class="btn">Search</button>
        </form>
    </div>

    @if($posts->count() > 0)
        @foreach($posts as $post)
            <article class="card">
                <h2 class="post-title">
                    <a href="{{ route('blog.show', $post) }}" style="text-decoration: none; color: inherit;">
                        {{ $post->title }}
                    </a>
                </h2>
                <div class="post-meta">
                    By {{ $post->user->name }} • {{ $post->created_at->format('M j, Y') }}
                </div>
                <div class="post-body">
                    {{ Str::limit($post->body, 200) }}
                </div>
                <div style="margin-top: 1rem;">
                    <a href="{{ route('blog.show', $post) }}" class="btn btn-secondary">Read More</a>
                </div>
            </article>
        @endforeach

        <div class="pagination">
            {{ $posts->links() }}
        </div>
    @else
        <div class="card">
            <p>No posts found. {{ request('search') ? 'Try a different search term.' : 'Be the first to write a post!' }}</p>
        </div>
    @endif
@endsection