<?php

namespace App\Http\Controllers\AuthAdmin;

use App\Http\Controllers\Controller;
use App\Services\AdminEmailVerificationService;
use App\Models\User;
use Illuminate\Http\Request;

class AdminEmailVerificationController extends Controller
{
    public function __construct(
        protected AdminEmailVerificationService $adminEmailVerificationService,
    ) {}

    public function verifiedSuccess($email, $token)
    {
        $verificationStatus = $this->adminEmailVerificationService->verifyEmail($email, $token);

        switch ($verificationStatus) {
            case 'success':
                return view('verify')->with(['message' => 'success']);
                break;
            case 'invalid':
                return view('verify')->with(['message' => 'invalid']);
                break;
            case 'already_verified':
                return view('verify')->with([
                    'message' => 'already_verified',
                ]);
                break;
            case 'expired':
                return view('verify')->with([
                    'message' => 'token_expired',
                    'email' => $email,
                ]);
                break;
            default:
                return view('verify')->with('message', 'error');
        }
    }

    public function resendEmailVerificationLink(Request $request)
    {
        return $this->adminEmailVerificationService->resendLink($request['email']);
    }

    public function checkVerify(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && $user->email_verified_at) {
            return response()->json([
                'message' => 'Email has already been verified'
            ]);
        } else {
            return response()->json([
                'message' => 'Email has not been verified'
            ]);
        }
    }
}