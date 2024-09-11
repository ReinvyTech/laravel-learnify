<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ResetPasswordService;
use App\Models\StudentUser;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function __construct(
        protected ResetPasswordService $resetPasswordService,
    ) {}

    public function sendCodeLink(Request $request)
    {
        $user = StudentUser::where('email', $request->email)->first();

        if ($user) {
            $this->resetPasswordService->sendVerificationlink($user);
            return response()->json([
                'status' => 'success',
                'message' => 'Verification code has been send'
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found'
            ]);
        }
    }

    public function checkCodeVerify(Request $request)
    {
        return $this->resetPasswordService->verifyCode($request);
    }

    public function resetPassword(Request $request)
    {
        return $this->resetPasswordService->resetPass($request);
    }
}
