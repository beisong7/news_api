<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $userData): User
    {
        $userData['password'] = Hash::make($userData['password']);
        return $this->userRepository->create($userData);
    }

    public function loginUser(array $credentials): array
    {
        try {
            if (!$token = Auth::guard('api')->attempt($credentials)) {
                return [
                    'success' => false,
                    'message' => 'Invalid Credentials',
                    'status' => 401
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60000,
                    'user' => Auth::guard('api')->user()
                ]
            ];

        } catch (JWTException $e) {
            return [
                'success' => false,
                'message' => 'Could not create token',
                'status' => 500
            ];
        }
    }

    public function logoutUser(): void
    {
        if (auth('api')->user()) {
            auth('api')->user()->tokens()->delete();
        }
        Auth::guard('api')->logout();
    }
}
