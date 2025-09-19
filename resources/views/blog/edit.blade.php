@extends('layouts.app')

@section('title', 'Edit Post - Mini Blog')

@section('content')
    <div class="card">
        <h1>Edit Post</h1>
        
        <form action="{{ route('blog.update', $post) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                @error('title')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="body">Content</label>
                <textarea id="body" name="body" rows="10" required>{{ old('body', $post->body) }}</textarea>
                @error('body')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn">Update Post</button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection