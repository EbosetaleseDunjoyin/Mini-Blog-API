@extends('layouts.app')

@section('title', 'Dashboard - Mini Blog')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Your Posts</h1>
        <a href="{{ route('blog.create') }}" class="btn">Write New Post</a>
    </div>

    <div id="loading" style="text-align: center; padding: 2rem;">
        <p>Loading your posts...</p>
    </div>

    <div id="postsContainer">
       
    </div>

    <div id="pagination" style="text-align: center; margin: 2rem 0;">
        
    </div>

    <script>
        let currentPage = 1;

        async function loadUserPosts(page = 1) {
            const loading = document.getElementById('loading');
            const container = document.getElementById('postsContainer');
            const pagination = document.getElementById('pagination');
            
            loading.style.display = 'block';
            container.innerHTML = '';
            pagination.innerHTML = '';
            
            // Check if user is authenticated
            if (!blogAPI.token) {
                loading.style.display = 'none';
                container.innerHTML = `
                    <div class="card">
                        <p style="color: red;">You need to be logged in to view your posts.</p>
                        <a href="/login" class="btn" style="margin-top: 1rem;">Login</a>
                    </div>
                `;
                return;
            }
            
            try {
                const response = await blogAPI.getUserPosts(page);
                
                loading.style.display = 'none';
                
                if (response.data && response.data.data.length > 0) {
                    const posts = response.data.data;
                    
                    posts.forEach(post => {
                        const postElement = createUserPostElement(post);
                        container.appendChild(postElement);
                    });
                    
                    // Create pagination
                    createPagination(response.data, pagination);
                } else {
                    container.innerHTML = `
                        <div class="card">
                            <p>You haven't written any posts yet.</p>
                            <a href="{{ route('blog.create') }}" class="btn" style="margin-top: 1rem;">Write Your First Post</a>
                        </div>
                    `;
                }
            } catch (error) {
                loading.style.display = 'none';
                
                if (error.message.includes('Unauthenticated')) {
                    container.innerHTML = `
                        <div class="card">
                            <p style="color: red;">Your session has expired. Please log in again.</p>
                            <a href="/login" class="btn" style="margin-top: 1rem;">Login</a>
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="card">
                            <p style="color: red;">Error loading posts: ${error.message}</p>
                        </div>
                    `;
                }
            }
        }

        function createUserPostElement(post) {
            const div = document.createElement('div');
            div.className = 'card';
            
            const truncatedBody = post.body.length > 200 ? 
                post.body.substring(0, 200) + '...' : 
                post.body;
                
            const updatedText = post.updated_at !== post.created_at ? 
                ` • Updated ${formatDate(post.updated_at)}` : '';
            
            div.innerHTML = `
                <h2 class="post-title">${escapeHtml(post.title)}</h2>
                <div class="post-meta">
                    Created ${formatDate(post.created_at)}${updatedText}
                </div>
                <div class="post-body">
                    ${escapeHtml(truncatedBody)}
                </div>
                <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="/post/${post.id}" class="btn btn-secondary">View</a>
                    <a href="/post/${post.id}/edit" class="btn">Edit</a>
                    <button type="button" class="btn btn-danger" onclick="deletePost(${post.id})">Delete</button>
                </div>
            `;
            
            return div;
        }

        function createPagination(paginationData, container) {
            if (paginationData.last_page <= 1) return;
            
            const paginationElement = document.createElement('div');
            paginationElement.style.cssText = 'display: flex; justify-content: center; gap: 0.5rem; margin: 2rem 0;';
            
            // Previous button
            if (paginationData.current_page > 1) {
                const prevBtn = document.createElement('button');
                prevBtn.textContent = 'Previous';
                prevBtn.className = 'btn btn-secondary';
                prevBtn.onclick = () => {
                    currentPage = paginationData.current_page - 1;
                    loadUserPosts(currentPage);
                };
                paginationElement.appendChild(prevBtn);
            }
            
            const startPage = Math.max(1, paginationData.current_page - 2);
            const endPage = Math.min(paginationData.last_page, paginationData.current_page + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.className = i === paginationData.current_page ? 'btn' : 'btn btn-secondary';
                pageBtn.onclick = () => {
                    currentPage = i;
                    loadUserPosts(currentPage);
                };
                paginationElement.appendChild(pageBtn);
            }
            

            if (paginationData.current_page < paginationData.last_page) {
                const nextBtn = document.createElement('button');
                nextBtn.textContent = 'Next';
                nextBtn.className = 'btn btn-secondary';
                nextBtn.onclick = () => {
                    currentPage = paginationData.current_page + 1;
                    loadUserPosts(currentPage);
                };
                paginationElement.appendChild(nextBtn);
            }
            
            container.appendChild(paginationElement);
        }

        async function deletePost(postId) {
            if (!confirm('Are you sure you want to delete this post?')) {
                return;
            }
            
            try {
                await blogAPI.deletePost(postId);
                showNotification('Post deleted successfully', 'success');
                loadUserPosts(currentPage); // Reload posts
            } catch (error) {
                showNotification(`Error deleting post: ${error.message}`, 'error');
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Load posts when page loads
        document.addEventListener('DOMContentLoaded', () => {
            loadUserPosts(currentPage);
        });
    </script>
@endsection