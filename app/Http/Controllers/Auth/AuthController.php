<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{

    public function __construct(protected EmailVerificationService $emailVerificationService) {}

    public function adminRegister(RegisterRequest $request)
    {
        if ($request->isAdmin != 'learnify2024admin') {
            return response()->json([
                'message' => 'Code access wrong',
            ]);
        } else {

            $user = User::create([
                'name' => $request->name,
                'role' => 'admin',
                'email' => $request->email,
                'password' => $request->password
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            $this->emailVerificationService->sendVerificationlink($user);

            return response()->json([
                'message' => 'Teacher registered successfully',
                'access_token' => $token,
                'data' => $user,
            ]);
        }
    }

    public function teacherRegister(RegisterRequest $request)
    {
        if ($request->isTeacher != 'learnify2024teacher') {
            return response()->json([
                'message' => 'Code access wrong',
            ]);
        } else {

            $user = User::create([
                'name' => $request->name,
                'role' => 'teacher',
                'email' => $request->email,
                'password' => $request->password
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            $this->emailVerificationService->sendVerificationlink($user);

            return response()->json([
                'message' => 'Teacher registered successfully',
                'access_token' => $token,
                'data' => $user,
            ]);
        }
    }

    public function studentRegister(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'role' => 'student',
            'email' => $request->email,
            'password' => $request->password
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $this->emailVerificationService->sendVerificationlink($user);

        return response()->json([
            'message' => 'Student registered successfully',
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

    public function students()
    {
        $student = User::where('role', 'student')->get();
        return response()->json([
            'message' => 'Get data success',
            'data' => $student,
        ]);
    }

    public function teachers()
    {
        $teacher = User::where('role', 'teacher')->get();
        return response()->json([
            'message' => 'Get data success',
            'data' => $teacher,
        ]);
    }
}
