@extends('layouts.app')

@section('title', 'Create New Post - Mini Blog')

@section('content')
    <div class="card">
        <h1>Create New Post</h1>
        
        <form action="{{ route('blog.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="body">Content</label>
                <textarea id="body" name="body" rows="10" required>{{ old('body') }}</textarea>
                @error('body')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn">Create Post</button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection