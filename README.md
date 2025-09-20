# 📝 Mini Blog API

<p align="center">
  <a href="#"><img src="https://img.shields.io/badge/version-1.0.0-blue" alt="Version"></a>
  <a href="#"><img src="https://img.shields.io/badge/PHP-8.2-777BB4.svg?logo=php" alt="PHP"></a>
  <a href="#"><img src="https://img.shields.io/badge/Laravel-12-FF2D20.svg?logo=laravel" alt="Laravel"></a>
  <a href="#"><img src="https://img.shields.io/badge/Laravel_Sanctum-4.0-4FC08D.svg" alt="Sanctum"></a>
  <a href="#"><img src="https://img.shields.io/badge/license-MIT-green" alt="License"></a>
</p>

## 📋 Overview

Mini Blog API is a comprehensive RESTful API for blog management built with Laravel and Sanctum authentication. The system provides both API endpoints and a simple web interface for creating, managing, and viewing blog posts with full user authentication and authorization.

## ✨ Key Features

### 🔐 Authentication & Authorization

- **User Registration & Login** - Secure user account creation and authentication
- **Laravel Sanctum** - Token-based API authentication for SPAs and mobile apps
- **Session-based Web Auth** - Traditional web authentication for the frontend
- **User Authorization** - Users can only manage their own posts

### 📝 Blog Management

- **Post CRUD Operations** - Create, read, update, and delete blog posts
- **Rich Content Support** - Title and body content with proper validation
- **Author Attribution** - Posts automatically linked to authenticated users
- **Timestamps** - Automatic creation and update tracking

### 🌐 Public Access

- **Public Post Viewing** - Anyone can view all posts without authentication
- **Individual Post Access** - Public access to specific post details
- **Search Functionality** - Search posts by title or content
- **Pagination** - Efficient browsing with configurable page sizes

### 🎨 Web Interface

- **Responsive Design** - Clean, mobile-friendly web interface
- **Public Blog Homepage** - Browse all posts with search functionality
- **User Dashboard** - Manage personal posts after authentication
- **Post Management** - Create, edit, and delete posts through web forms
- **Authentication Pages** - Login and registration forms

### 📊 Additional Features

- **API Documentation** - Auto-generated documentation with Scramble
- **Model Factories** - Realistic test data generation
- **Database Seeders** - Pre-populated sample content
- **Comprehensive Testing** - Test coverage for API endpoints
- **Error Handling** - Proper HTTP status codes and error messages

## 🔧 Technologies Used

### Backend

- **Laravel 12** - PHP Framework with latest features
- **Laravel Sanctum 4.0** - API Authentication
- **MySql** - Database for development
- **Pest** - Modern PHP testing framework
- **Faker** - Realistic test data generation

### Frontend

- **Blade Templates** - Server-side rendering
- **Tailwind CSS 4.0** - Utility-first CSS framework
- **Vanilla JavaScript** - Lightweight frontend interactions
- **Responsive Design** - Mobile-first approach

### Development Tools

- **Scramble** - API documentation generation
- **Laravel Pint** - Code formatting
- **Vite** - Fast build tool and development server

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (included with PHP)

### Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/EbosetaleseDunjoyin/mini-blog-api.git
   cd mini-blog-api
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**

   ```bash
   npm install
   ```

4. **Setup environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**

   The project uses MySql by default.

6. **Run migrations and seed data**

   ```bash
   php artisan migrate --seed
   ```

7. **Build frontend assets**

   ```bash
   npm run build
   ```

8. **Start the development server**

   ```bash
   php artisan serve
   ```

   Your application will be available at `http://localhost:8000`

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication Endpoints

```http
POST /api/v1/auth/register    # User registration
POST /api/v1/auth/login       # User login
GET  /api/v1/auth/me          # Get current user (protected)
POST /api/v1/auth/logout      # User logout (protected)
```

### Public Endpoints

```http
GET /api/v1/public/posts                    # Get all posts (with pagination)
GET /api/v1/public/posts/{id}               # Get specific post
GET /api/v1/public/posts?q=keyword   # Search posts
```

### Protected Endpoints (Require Authentication)

```http
GET    /api/v1/posts           # Get user's posts
POST   /api/v1/posts           # Create new post
GET    /api/v1/posts/{id}      # Get user's specific post
PUT    /api/v1/posts/{id}      # Update user's post
DELETE /api/v1/posts/{id}      # Delete user's post
```

### Auto-Generated Documentation

Visit `/docs/api/v1` after running the application to see the complete auto-generated API documentation powered by Scramble.

## 🎯 Web Interface

### Public Pages
- **Homepage** (`/`) - Browse all blog posts with search
- **Post View** (`/post/{id}`) - View individual blog post

### Authentication
- **Login** (`/login`) - User login form
- **Register** (`/register`) - User registration form

### User Dashboard
- **Dashboard** (`/dashboard`) - Manage your posts
- **Create Post** (`/create`) - Write new blog post
- **Edit Post** (`/posts/{id}/edit`) - Edit existing post

## 🗄️ Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `created_at`, `updated_at` - Timestamps

### Posts Table
- `id` - Primary key
- `title` - Post title
- `body` - Post content
- `user_id` - Foreign key to users table
- `created_at`, `updated_at` - Timestamps

### Personal Access Tokens (Sanctum)
- Authentication tokens for API access

## 🎭 Test Data

The application includes realistic test data:

- **Test Users:**
  - Email: `john@example.com` / Password: `password123`
  - Email: `jane@example.com` / Password: `password123`
- **Sample Posts:** Various blog posts with realistic content
- **Additional Users:** Random generated users with associated posts

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

The project includes comprehensive tests for:
- User authentication
- Post CRUD operations
- API endpoints
- Authorization checks
- Search functionality


## 🚀 Production Deployment

For production deployment:

1. **Set environment variables**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```

2. **Use a production database**
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=your-host
   DB_DATABASE=your-database
   ```

3. **Optimize application**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   composer install --optimize-autoloader --no-dev
   ```

4. **Set up HTTPS** for secure API token transmission

## 📝 License

This project is licensed under the MIT License.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📞 Support

For issues and questions:
- Create an issue on GitHub
- Check the API documentation
- Review the test files for usage examples

---

**Built with ❤️ using Laravel and modern web technologies**