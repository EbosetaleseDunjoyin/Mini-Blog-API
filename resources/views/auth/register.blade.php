@extends('layouts.app')

@section('title', 'Register - Mini Blog')

@section('content')
    <div class="card" style="max-width: 400px; margin: 0 auto;">
        <h1>Register</h1>
        
        <form id="registerForm">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
                <div id="nameError" class="error" style="display: none;"></div>
            </div>
            
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
            
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <div id="passwordConfirmationError" class="error" style="display: none;"></div>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;" id="submitBtn">Register</button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem;">
            <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            
            // Error elements
            const nameError = document.getElementById('nameError');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const passwordConfirmationError = document.getElementById('passwordConfirmationError');
            
            // Clear previous errors
            nameError.style.display = 'none';
            emailError.style.display = 'none';
            passwordError.style.display = 'none';
            passwordConfirmationError.style.display = 'none';
            
            // Client-side validation
            if (passwordInput.value !== passwordConfirmationInput.value) {
                passwordConfirmationError.textContent = 'Passwords do not match';
                passwordConfirmationError.style.display = 'block';
                return;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating Account...';
            
            try {
                const response = await blogAPI.register(
                    nameInput.value.trim(),
                    emailInput.value.trim(),
                    passwordInput.value,
                    passwordConfirmationInput.value
                );
                
                showNotification('Registration successful!', 'success');
                
                // Redirect to dashboard after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("dashboard") }}';
                }, 1000);
                
            } catch (error) {
                // Handle validation errors
                if (error.message.includes('Validation') || error.message.includes('422')) {
                    try {
                        // Try to get detailed error response
                        const errorResponse = await fetch(`${blogAPI.baseURL}/auth/register`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': blogAPI.csrfToken
                            },
                            body: JSON.stringify({
                                name: nameInput.value.trim(),
                                email: emailInput.value.trim(),
                                password: passwordInput.value,
                                password_confirmation: passwordConfirmationInput.value
                            })
                        });
                        
                        const errorData = await errorResponse.json();
                        
                        // Display field-specific errors
                        if (errorData.errors) {
                            if (errorData.errors.name) {
                                nameError.textContent = errorData.errors.name[0];
                                nameError.style.display = 'block';
                            }
                            if (errorData.errors.email) {
                                emailError.textContent = errorData.errors.email[0];
                                emailError.style.display = 'block';
                            }
                            if (errorData.errors.password) {
                                passwordError.textContent = errorData.errors.password[0];
                                passwordError.style.display = 'block';
                            }
                            if (errorData.errors.password_confirmation) {
                                passwordConfirmationError.textContent = errorData.errors.password_confirmation[0];
                                passwordConfirmationError.style.display = 'block';
                            }
                        } else {
                            showNotification(errorData.message || 'Registration failed', 'error');
                        }
                    } catch (parseError) {
                        showNotification('Please check your input and try again', 'error');
                    }
                } else {
                    showNotification(`Registration failed: ${error.message}`, 'error');
                }
            } finally {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Register';
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