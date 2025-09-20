@extends('layouts.app')

@section('title', 'Edit Post - Mini Blog')

@section('content')
    <div id="loading" style="text-align: center; padding: 2rem;">
        <p>Loading post...</p>
    </div>

    <div id="editFormContainer" style="display: none;">
        <div class="card">
            <h1>Edit Post</h1>
            
            <form id="editPostForm">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                    <div id="titleError" class="error" style="display: none;"></div>
                </div>
                
                <div class="form-group">
                    <label for="body">Content</label>
                    <textarea id="body" name="body" rows="10" required></textarea>
                    <div id="bodyError" class="error" style="display: none;"></div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn" id="submitBtn">Update Post</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div id="errorContainer" style="display: none;">
        <div class="card">
            <p style="color: red;">Error loading post. You may not have permission to edit this post.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script>
        let postId = null;

        async function loadPost() {
            const loading = document.getElementById('loading');
            const formContainer = document.getElementById('editFormContainer');
            const errorContainer = document.getElementById('errorContainer');
            
            // Get post ID from URL
            const pathSegments = window.location.pathname.split('/');
            postId = pathSegments[pathSegments.length - 1];
            
            if (!postId || isNaN(postId)) {
                loading.style.display = 'none';
                errorContainer.style.display = 'block';
                return;
            }
            
            // Check if user is authenticated
            if (!blogAPI.token) {
                loading.style.display = 'none';
                errorContainer.innerHTML = `
                    <div class="card">
                        <p style="color: red;">You need to be logged in to edit posts.</p>
                        <a href="/login" class="btn">Login</a>
                    </div>
                `;
                errorContainer.style.display = 'block';
                return;
            }
            
            try {
                const response = await blogAPI.getPost(postId);
                
                loading.style.display = 'none';
                
                if (response.data) {
                    const post = response.data;
                    
                    // Update page title
                    document.title = `Edit ${post.title} - Mini Blog`;
                    
                    // Populate form
                    document.getElementById('title').value = post.title;
                    document.getElementById('body').value = post.body;
                    
                    formContainer.style.display = 'block';
                } else {
                    errorContainer.style.display = 'block';
                }
            } catch (error) {
                loading.style.display = 'none';
                
                if (error.message.includes('Unauthenticated')) {
                    errorContainer.innerHTML = `
                        <div class="card">
                            <p style="color: red;">Your session has expired. Please log in again.</p>
                            <a href="/login" class="btn">Login</a>
                        </div>
                    `;
                } else if (error.message.includes('Unauthorized') || error.message.includes('403')) {
                    errorContainer.innerHTML = `
                        <div class="card">
                            <p style="color: red;">You don't have permission to edit this post.</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                        </div>
                    `;
                } else {
                    errorContainer.innerHTML = `
                        <div class="card">
                            <p style="color: red;">Error loading post: ${error.message}</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                        </div>
                    `;
                }
                
                errorContainer.style.display = 'block';
            }
        }

        document.getElementById('editPostForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const titleInput = document.getElementById('title');
            const bodyInput = document.getElementById('body');
            const titleError = document.getElementById('titleError');
            const bodyError = document.getElementById('bodyError');
            
            // Clear previous errors
            titleError.style.display = 'none';
            bodyError.style.display = 'none';
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
            
            try {
                const response = await blogAPI.updatePost(
                    postId,
                    titleInput.value.trim(),
                    bodyInput.value.trim()
                );
                
                showNotification('Post updated successfully!', 'success');
                
                // Redirect to dashboard after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("dashboard") }}';
                }, 1000);
                
            } catch (error) {
                // Handle validation errors
                if (error.message.includes('Validation')) {
                    try {
                        // Try to parse the error response for validation details
                        const errorResponse = await fetch(`${blogAPI.baseURL}/posts/${postId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${blogAPI.token}`,
                                'X-CSRF-TOKEN': blogAPI.csrfToken
                            },
                            body: JSON.stringify({
                                title: titleInput.value.trim(),
                                body: bodyInput.value.trim()
                            })
                        });
                        
                        const errorData = await errorResponse.json();
                        
                        // Display field-specific errors
                        if (errorData.errors) {
                            if (errorData.errors.title) {
                                titleError.textContent = errorData.errors.title[0];
                                titleError.style.display = 'block';
                            }
                            if (errorData.errors.body) {
                                bodyError.textContent = errorData.errors.body[0];
                                bodyError.style.display = 'block';
                            }
                        } else {
                            showNotification(errorData.message || 'Validation failed', 'error');
                        }
                    } catch (parseError) {
                        showNotification('Please check your input and try again', 'error');
                    }
                } else if (error.message.includes('Unauthenticated')) {
                    showNotification('Your session has expired. Please log in again.', 'error');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else if (error.message.includes('Unauthorized')) {
                    showNotification('You don\'t have permission to edit this post', 'error');
                } else {
                    showNotification(`Error updating post: ${error.message}`, 'error');
                }
            } finally {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Update Post';
            }
        });

        // Load post when page loads
        document.addEventListener('DOMContentLoaded', loadPost);
    </script>
@endsection