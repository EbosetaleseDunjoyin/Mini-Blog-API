@extends('layouts.app')

@section('title', 'Login - Mini Blog')

@section('content')
    <div class="card" style="max-width: 400px; margin: 0 auto;">
        <h1>Login</h1>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <div id="emailError" class="error" style="display: none;"></div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <div id="passwordError" class="error" style="display: none;"></div>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;" id="submitBtn">Login</button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem;">
            <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            
            // Clear previous errors
            emailError.style.display = 'none';
            passwordError.style.display = 'none';
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging in...';
            
            try {
                const response = await blogAPI.login(
                    emailInput.value.trim(),
                    passwordInput.value
                );
                
                showNotification('Login successful!', 'success');
                
                // Redirect to dashboard after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("dashboard") }}';
                }, 1000);
                
            } catch (error) {
                // Handle validation errors
                if (error.message.includes('Validation') || error.message.includes('422')) {
                    try {
                        // Try to get detailed error response
                        const errorResponse = await fetch(`${blogAPI.baseURL}/auth/login`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': blogAPI.csrfToken
                            },
                            body: JSON.stringify({
                                email: emailInput.value.trim(),
                                password: passwordInput.value
                            })
                        });
                        
                        const errorData = await errorResponse.json();
                        
                        // Display field-specific errors
                        if (errorData.errors) {
                            if (errorData.errors.email) {
                                emailError.textContent = errorData.errors.email[0];
                                emailError.style.display = 'block';
                            }
                            if (errorData.errors.password) {
                                passwordError.textContent = errorData.errors.password[0];
                                passwordError.style.display = 'block';
                            }
                        } else {
                            showNotification(errorData.message || 'Invalid credentials', 'error');
                        }
                    } catch (parseError) {
                        showNotification('Please check your credentials and try again', 'error');
                    }
                } else if (error.message.includes('401') || error.message.includes('Invalid credentials')) {
                    showNotification('Invalid email or password', 'error');
                } else {
                    showNotification(`Login failed: ${error.message}`, 'error');
                }
            } finally {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Login';
            }
        });

        // Check if user is already logged in
        document.addEventListener('DOMContentLoaded', () => {
            if (blogAPI.token) {
                // User is already logged in, redirect to dashboard
                window.location.href = '{{ route("dashboard") }}';
            }
        });
    </script>
@endsection