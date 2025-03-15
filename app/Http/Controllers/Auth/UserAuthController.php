<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required|string',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->authService->registerUser($validator->validated());

        return response()->json([
            'message' => 'User Created',
        ]);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->loginUser($request->validated());

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return response()->json($result['data']);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logoutUser();

        return response()->json([
            "message" => "logged out"
        ]);
    }
}
