
    class MiniBlogAPI {
        constructor() {
            this.baseURL = '/api/v1';
            this.token = localStorage.getItem('auth_token');
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        }
        
        async request(endpoint, options = {}) {
            const url = `${this.baseURL}${endpoint}`;
            const config = {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    ...options.headers
                },
                ...options
            };
            
            if (this.token) {
                config.headers['Authorization'] = `Bearer ${this.token}`;
            }
            
            try {
                const response = await fetch(url, config);
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Request failed');
                }
                
                return data;
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        }
        async updateAuthUI() {
            const isAuthenticated = await this.isTokenValid();
            
            const authElements = document.querySelectorAll('.auth');
            const guestElements = document.querySelectorAll('.guest');
            
            authElements.forEach(element => {
                element.style.display = isAuthenticated ? 'block' : 'none';
            });
            
            guestElements.forEach(element => {
                element.style.display = isAuthenticated ? 'none' : 'block';
            });
        }
        async login(email, password) {
            const data = await this.request('/auth/login', {
                method: 'POST',
                body: JSON.stringify({ email, password })
            });
            
            if (data.data && data.data.token) {
                this.token = data.data.token;
                localStorage.setItem('auth_token', this.token);
            }
            
            return data;
        }
        
        async register(name, email, password, password_confirmation) {
            const data = await this.request('/auth/register', {
                method: 'POST',
                body: JSON.stringify({ name, email, password, password_confirmation })
            });
            
            if (data.data && data.data.token) {
                this.token = data.data.token;
                localStorage.setItem('auth_token', this.token);
            }
            
            return data;
        }

        async checkAuth() {
            return !!this.token && await this.isTokenValid();     
        }
        
        async isTokenValid() {
            try {
                await this.getUser();
                return true;
            } catch (error) {
                if(this.token) {
                    this.logout();
                }
                return false;
            }
        }

        logout() {
            this.token = null;
            localStorage.removeItem('auth_token');
        }
        
        // Public post methods
        async getPublicPosts(page = 1, search = '') {
            const params = new URLSearchParams({ page });
            if (search) params.append('search', search);
            return this.request(`/public/posts?${params}`);
        }
        
        async getPublicPost(id) {
            return this.request(`/public/posts/${id}`);
        }

        async getUser() {
            return await this.request('/auth/me');
        }
        
        // Authenticated post methods
        async getUserPosts(page = 1) {
            if (!this.checkAuth()) {
                throw new Error('Not authenticated');
            }
            const params = new URLSearchParams({ page });
            return this.request(`/posts?${params}`);
        }
        
        async getPost(id) {
            return this.request(`/posts/${id}`);
        }
        
        async createPost(title, body) {
            if (!this.checkAuth()) {
                throw new Error('Not authenticated');
            }
            return this.request('/posts', {
                method: 'POST',
                body: JSON.stringify({ title, body })
            });
        }
        
        async updatePost(id, title, body) {
            if (!this.checkAuth()) {
                throw new Error('Not authenticated');
            }
            return this.request(`/posts/${id}`, {
                method: 'PUT',
                body: JSON.stringify({ title, body })
            });
        }
        
        async deletePost(id) {
            if (!this.checkAuth()) {
                throw new Error('Not authenticated');
            }
            return this.request(`/posts/${id}`, {
                method: 'DELETE'
            });
        }
    }
    
    window.blogAPI = new MiniBlogAPI();
    
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = type;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            z-index: 1000;
            max-width: 300px;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
    
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    function handleLogout() {
        blogAPI.logout();
        showNotification('Logged out successfully', 'success');
        setTimeout(() => {
            window.location.href = '/';
        }, 1000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        blogAPI.updateAuthUI();
    });