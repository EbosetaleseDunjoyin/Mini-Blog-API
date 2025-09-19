# Mini Blog API Documentation

## Overview
This is a RESTful API for a mini blog application built with Laravel and Sanctum authentication.

## Base URL
```
http://localhost:8000/api
```

## Authentication
The API uses Laravel Sanctum for token-based authentication.

### Register a new user
```http
POST /auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Login
```http
POST /auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

### Get current user
```http
GET /auth/me
Authorization: Bearer {token}
```

### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

## Public Endpoints (No Authentication Required)

### Get all posts
```http
GET /public/posts?page=1&per_page=10&search=laravel
```

### Get a single post
```http
GET /public/posts/{id}
```

### Search posts
```http
GET /public/posts/search?q=laravel&per_page=10
```

## Protected Endpoints (Authentication Required)

### Get user's posts
```http
GET /posts
Authorization: Bearer {token}
```

### Create a new post
```http
POST /posts
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "My New Post",
    "body": "This is the content of my new post..."
}
```

### Get a specific post (user's own)
```http
GET /posts/{id}
Authorization: Bearer {token}
```

### Update a post
```http
PUT /posts/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Updated Post Title",
    "body": "Updated content..."
}
```

### Delete a post
```http
DELETE /posts/{id}
Authorization: Bearer {token}
```

## Frontend Web Interface

The application also includes a simple web interface accessible at:

- **Home Page**: `/` - View all posts
- **Single Post**: `/post/{id}` - View a specific post
- **Login**: `/login` - User login form
- **Register**: `/register` - User registration form
- **Dashboard**: `/dashboard` - User's post management (requires login)
- **Create Post**: `/create` - Create new post form (requires login)
- **Edit Post**: `/posts/{id}/edit` - Edit post form (requires login)

## Features Implemented

✅ **Authentication**
- User registration and login with Laravel Sanctum
- Token-based API authentication
- Session-based web authentication

✅ **Blog Posts**
- CRUD operations for authenticated users
- Users can only manage their own posts
- Title, body, author, and timestamps

✅ **Public Access**
- Anyone can view the list of posts
- Anyone can view individual posts

✅ **Bonus Features**
- Search posts by title or body content
- Pagination for post listings
- Simple frontend with Blade templates
- Responsive design with clean CSS

## Test Data
The application includes test users and posts:
- Email: john@example.com, Password: password123
- Email: jane@example.com, Password: password123

## Running the Application

1. Start the development server:
   ```bash
   php artisan serve
   ```

2. The API will be available at: `http://localhost:8000/api`
3. The web interface will be available at: `http://localhost:8000`

## Database Schema

### Users Table
- id (primary key)
- name
- email (unique)
- password
- created_at
- updated_at

### Posts Table
- id (primary key)
- title
- body (text)
- user_id (foreign key to users)
- created_at
- updated_at

### Personal Access Tokens Table (Sanctum)
- id (primary key)
- tokenable_type
- tokenable_id
- name
- token (hashed)
- abilities (json)
- last_used_at
- expires_at
- created_at
- updated_at