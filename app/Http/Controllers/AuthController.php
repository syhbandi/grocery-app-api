<?php

namespace App\Http\Controllers;

use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\registerRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(registerRequest $registerRequest): JsonResponse
    {
        $user = new User($registerRequest->all());
        $user->password = Hash::make($registerRequest->password);
        $user->save();

        $ability = $registerRequest->has('role') && $registerRequest->role == 'admin' ? ['admin'] : ['user'];
        $token = $user->createToken('access_token', $ability)->plainTextToken;

        return response()->json(['token' => $token, 'user' => new UserResource($user)], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $loginRequest): JsonResponse
    {
        $data = $loginRequest->validated();

        if (!Auth::attempt($data)) {
            return response()->json(['messages' => 'Email or Password wrong'], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $ability = $user->role == 'admin' ? ['admin'] : ['user'];
        $token = $loginRequest->user()->createToken('access_token', $ability)->plainTextToken;
        return response()->json(['token' => $token, 'user' => new UserResource($user)], Response::HTTP_OK);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], Response::HTTP_OK);
    }

    public function user(Request $request)
    {
        return new UserResource($request->user());
    }
}
