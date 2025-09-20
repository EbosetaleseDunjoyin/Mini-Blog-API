<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponder;

    public function __construct(
        private  AuthService $authService
    )
    {

    }
    /**
     * Register a new user
     * @unauthenticated
     * @param  Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
       $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try{
            DB::beginTransaction();
            $user = $this->authService->createUser($request->only(['name', 'email', 'password']));
            $token = $user->createToken('miniblogapi')->plainTextToken;
            DB::commit();
            return $this->successResponse("Registration successful", [
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error: ' . $e->getMessage());
            return $this->errorResponse('Registration failed', 500);
        }

    }

       
    /**
     * Login
     * 
     * @unauthenticated
     * @param  Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        $user = $this->authService->verifyCredentials($request->email, $request->password);

        if (!$user) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $token = $user->createToken('miniblogapi')->plainTextToken;

        return $this->successResponse('Login successful', [
            'token' => $token,
            'user' => $user,
        ]);
    }


    /**
     * Logout
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->successResponse('Logout successful', [], 200);
    }

    /**
     * Get the authenticated User
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse('User retrieved successfully', [
            'user' => $request->user()
        ]);
        
    }
}
