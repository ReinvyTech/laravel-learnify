<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\EmailVerificationService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{

    public function __construct(protected EmailVerificationService $emailVerificationService) {}

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $this->emailVerificationService->sendVerificationlink($user);

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'data' => $user,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();


        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'access_token' => $token,
        ]);
    }

    public function profile()
    {
        return response()->json([
            'message' => 'Get data success',
            'data' => auth()->user(),
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout success'
        ]);
    }
}
