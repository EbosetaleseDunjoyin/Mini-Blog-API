@extends('layouts.app')

@section('title', 'Post - Mini Blog')

@section('content')
    <div id="loading" style="text-align: center; padding: 2rem;">
        <p>Loading post...</p>
    </div>

    <div id="postContainer">
        <!-- Post will be loaded here via JavaScript -->
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('blog.index') }}" class="btn btn-secondary">← Back to All Posts</a>
    </div>

    <script>
        async function loadPost() {
            const loading = document.getElementById('loading');
            const container = document.getElementById('postContainer');
            
            // Get post ID from URL
            const pathSegments = window.location.pathname.split('/');
            const postId = pathSegments[pathSegments.length - 1];
            
            if (!postId || isNaN(postId)) {
                loading.style.display = 'none';
                container.innerHTML = `
                    <div class="card">
                        <p style="color: red;">Invalid post ID</p>
                    </div>
                `;
                return;
            }
            
            try {
                const response = await blogAPI.getPublicPost(postId);
                
                loading.style.display = 'none';
                
                if (response.data) {
                    const post = response.data;
                    
                    // Update page title
                    document.title = `${post.title} - Mini Blog`;
                    
                    container.innerHTML = `
                        <article class="card">
                            <h1 class="post-title">${escapeHtml(post.title)}</h1>
                            <div class="post-meta">
                                By ${escapeHtml(post.user.name)} • ${formatDate(post.created_at)}
                            </div>
                            <div class="post-body">${escapeHtml(post.body).replace(/\n/g, '<br>')}</div>
                        </article>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="card">
                            <p style="color: red;">Post not found</p>
                        </div>
                    `;
                }
            } catch (error) {
                loading.style.display = 'none';
                container.innerHTML = `
                    <div class="card">
                        <p style="color: red;">Error loading post: ${error.message}</p>
                        <p>This post may not exist or may be private.</p>
                    </div>
                `;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Load post when page loads
        document.addEventListener('DOMContentLoaded', loadPost);
    </script>
@endsection