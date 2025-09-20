@extends('layouts.app')

@section('title', 'Create New Post - Mini Blog')

@section('content')
    <div class="card">
        <h1>Create New Post</h1>
        
        <form id="createPostForm">
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
                <button type="submit" class="btn" id="submitBtn">Create Post</button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            if (!blogAPI?.token) {
                document.getElementById('createPostForm').style.display = 'none';
                const container = document.querySelector('.card');
                container.innerHTML = `
                    <div class="card">
                        <p style="color: red;">You need to be logged in to view your posts.</p>
                        <a href="/login" class="btn" style="margin-top: 1rem;">Login</a>
                    </div>
                `;
            }
        });
        document.getElementById('createPostForm').addEventListener('submit', async (e) => {
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
            submitBtn.textContent = 'Creating...';
            
            // Check if user is authenticated
            if (!blogAPI.token) {
                showNotification('You need to be logged in to create a post', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Post';
                return;
            }
            
            try {
                const response = await blogAPI.createPost(
                    titleInput.value.trim(),
                    bodyInput.value.trim()
                );
                
                showNotification('Post created successfully!', 'success');
                
                // Redirect to dashboard after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("dashboard") }}';
                }, 1000);
                
            } catch (error) {
                // Handle validation errors
                if (error.message.includes('Validation')) {
                    try {
                        // Try to parse the error response for validation details
                        const errorResponse = await fetch(`${blogAPI.baseURL}/posts`, {
                            method: 'POST',
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
                } else {
                    showNotification(`Error creating post: ${error.message}`, 'error');
                }
            } finally {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Post';
            }
        });

        // Auto-save draft to localStorage (optional feature)
        function saveDraft() {
            const title = document.getElementById('title').value;
            const body = document.getElementById('body').value;
            
            if (title || body) {
                localStorage.setItem('postDraft', JSON.stringify({ title, body }));
            }
        }

        function loadDraft() {
            const draft = localStorage.getItem('postDraft');
            if (draft) {
                try {
                    const { title, body } = JSON.parse(draft);
                    document.getElementById('title').value = title || '';
                    document.getElementById('body').value = body || '';
                } catch (e) {
                    // Ignore invalid draft
                }
            }
        }

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', loadDraft);

        // Save draft on input
        document.getElementById('title').addEventListener('input', saveDraft);
        document.getElementById('body').addEventListener('input', saveDraft);

        // Clear draft on successful submission
        document.addEventListener('postCreated', () => {
            localStorage.removeItem('postDraft');
        });
    </script>
@endsection