@extends('layouts.app')

@section('title', 'All Posts - Mini Blog')

@section('content')
    <div class="search-form">
        <div style="display: flex; width: 100%;">
            <input type="text" id="searchInput" placeholder="Search posts..." style="flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px 0 0 4px;">
            <button type="button" id="searchBtn" class="btn" style="border-radius: 0 4px 4px 0;">Search</button>
        </div>
    </div>

    <div id="loading" style="text-align: center; padding: 2rem;">
        <p>Loading posts...</p>
    </div>

    <div id="postsContainer">
       
    </div>

    <div id="pagination" style="text-align: center; margin: 2rem 0;">
        
    </div>

    <script>
        let currentPage = 1;
        let currentSearch = '';

        async function loadPosts(page = 1, search = '') {
            const loading = document.getElementById('loading');
            const container = document.getElementById('postsContainer');
            const pagination = document.getElementById('pagination');
            
            loading.style.display = 'block';
            container.innerHTML = '';
            pagination.innerHTML = '';
            
            try {
                const response = await blogAPI.getPublicPosts(page, search);
                
                loading.style.display = 'none';
                
                if (response.data && response.data.data.length > 0) {
                    const posts = response.data.data;
                    
                    posts.forEach(post => {
                        const postElement = createPostElement(post);
                        container.appendChild(postElement);
                    });
                    
                    // Create pagination
                    createPagination(response.data, pagination);
                } else {
                    container.innerHTML = `
                        <div class="card">
                            <p>No posts found. ${search ? 'Try a different search term.' : 'Be the first to write a post!'}</p>
                        </div>
                    `;
                }
            } catch (error) {
                loading.style.display = 'none';
                container.innerHTML = `
                    <div class="card">
                        <p style="color: red;">Error loading posts: ${error.message}</p>
                    </div>
                `;
            }
        }

        function createPostElement(post) {
            const article = document.createElement('article');
            article.className = 'card';
            
            const truncatedBody = post.body.length > 200 ? 
                post.body.substring(0, 200) + '...' : 
                post.body;
            
            article.innerHTML = `
                <h2 class="post-title">
                    <a href="/blog/${post.id}" style="text-decoration: none; color: inherit;">
                        ${escapeHtml(post.title)}
                    </a>
                </h2>
                <div class="post-meta">
                    By ${escapeHtml(post.user.name)} • ${formatDate(post.created_at)}
                </div>
                <div class="post-body">
                    ${escapeHtml(truncatedBody)}
                </div>
                <div style="margin-top: 1rem;">
                    <a href="/post/${post.id}" class="btn btn-secondary">Read More</a>
                </div>
            `;
            
            return article;
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
                    loadPosts(currentPage, currentSearch);
                };
                paginationElement.appendChild(prevBtn);
            }
            
            // Page numbers
            const startPage = Math.max(1, paginationData.current_page - 2);
            const endPage = Math.min(paginationData.last_page, paginationData.current_page + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.className = i === paginationData.current_page ? 'btn' : 'btn btn-secondary';
                pageBtn.onclick = () => {
                    currentPage = i;
                    loadPosts(currentPage, currentSearch);
                };
                paginationElement.appendChild(pageBtn);
            }
            
            // Next button
            if (paginationData.current_page < paginationData.last_page) {
                const nextBtn = document.createElement('button');
                nextBtn.textContent = 'Next';
                nextBtn.className = 'btn btn-secondary';
                nextBtn.onclick = () => {
                    currentPage = paginationData.current_page + 1;
                    loadPosts(currentPage, currentSearch);
                };
                paginationElement.appendChild(nextBtn);
            }
            
            container.appendChild(paginationElement);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Event listeners
        document.getElementById('searchBtn').addEventListener('click', () => {
            currentSearch = document.getElementById('searchInput').value;
            currentPage = 1;
            loadPosts(currentPage, currentSearch);
        });

        document.getElementById('searchInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                currentSearch = document.getElementById('searchInput').value;
                currentPage = 1;
                loadPosts(currentPage, currentSearch);
            }
        });

        // Load initial posts
        document.addEventListener('DOMContentLoaded', () => {
            // Get search from URL if present
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            if (searchParam) {
                document.getElementById('searchInput').value = searchParam;
                currentSearch = searchParam;
            }
            
            loadPosts(currentPage, currentSearch);
        });
    </script>
@endsection