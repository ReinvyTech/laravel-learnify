<?php

namespace App\Http\Controllers\AuthAdmin;

use App\Http\Controllers\Controller;
use App\Services\AdminEmailVerificationService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AdminRegisterRequest;
use App\Models\StudentUser;
use App\Models\User;

class AdminAuthController extends Controller
{
    public function __construct(protected AdminEmailVerificationService $adminEmailVerificationService) {}

    public function register(AdminRegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'role' => $request->role,
            'email' => $request->email,
            'password' => $request->password
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $this->adminEmailVerificationService->sendVerificationlink($user);

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

    public function students()
    {
        $student =  StudentUser::all();
        return response()->json([
            'message' => 'Get data success',
            'data' => $student,
        ]);
    }

    public function teachers()
    {
        $teachers = User::where('role', 'teacher')->get();
        return response()->json([
            'message' => 'Get data success',
            'data' => $teachers,
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
