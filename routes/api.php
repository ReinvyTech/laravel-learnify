<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('register');
Route::get('/success={email}&{token}', [EmailVerificationController::class, 'verifiedSuccess']);
Route::post('/resend_email', [EmailVerificationController::class, 'resendEmailVerificationLink'])->name('resendEmailVerificationLink');
Route::post('/check_verify_email', [EmailVerificationController::class, 'checkVerify'])->name('checkVerify');

Route::get('/auth/{driver}', [SocialLoginController::class, 'toProvider'])->where('driver', 'github|google|facebook');
Route::get('/callback/{driver}/login', [SocialLoginController::class, 'handleCallback'])->where('driver', 'github|google|facebook');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
