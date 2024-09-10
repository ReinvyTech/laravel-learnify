<?php

use App\Http\Controllers\Auth\AdminEmailVerificationController;
use App\Http\Controllers\Auth\AdminResetPasswordController;
use App\Http\Controllers\Auth\AdminUserChangeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\UserChangeController;
use App\Http\Controllers\AuthAdmin\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register'])->name('adminlogin');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('adminregister');

    Route::get('/success={email}&{token}', [AdminEmailVerificationController::class, 'verifiedSuccess']);
    Route::post('/resend_email', [AdminEmailVerificationController::class, 'resendEmailVerificationLink'])->name('adminresendEmailVerificationLink');
    Route::post('/check_verify_email', [AdminEmailVerificationController::class, 'checkVerify'])->name('admincheckVerify');

    Route::post('/send_reset_code', [AdminResetPasswordController::class, 'sendCodeLink'])->name('adminsendCodeLink');
    Route::post('/check_code_verify', [AdminResetPasswordController::class, 'checkCodeVerify'])->name('admincheckCodeVerify');
    Route::post('/reset', [AdminResetPasswordController::class, 'resetPassword'])->name('adminresetPassword');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/change_password', [AdminUserChangeController::class, 'changeUserPassword'])->name('adminchangeUserPassword');
        Route::post('/update_name', [AdminUserChangeController::class, 'changeName'])->name('adminchangeName');

        Route::get('/all_students', [AdminAuthController::class, 'students'])->name('adminstudents');
        Route::get('/all_teachers', [AdminAuthController::class, 'teachers'])->name('adminteachers');
        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('adminprofile');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('adminlogout');
    });
});

Route::post('/register', [AuthController::class, 'register'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('register');

Route::get('/success={email}&{token}', [EmailVerificationController::class, 'verifiedSuccess']);
Route::post('/resend_email', [EmailVerificationController::class, 'resendEmailVerificationLink'])->name('resendEmailVerificationLink');
Route::post('/check_verify_email', [EmailVerificationController::class, 'checkVerify'])->name('checkVerify');

Route::post('/send_reset_code', [ResetPasswordController::class, 'sendCodeLink'])->name('sendCodeLink');
Route::post('/check_code_verify', [ResetPasswordController::class, 'checkCodeVerify'])->name('checkCodeVerify');
Route::post('/reset', [ResetPasswordController::class, 'resetPassword'])->name('resetPassword');

Route::get('/auth/{driver}', [SocialLoginController::class, 'toProvider'])->where('driver', 'github|google|facebook');
Route::get('/callback/{driver}/login', [SocialLoginController::class, 'handleCallback'])->where('driver', 'github|google|facebook');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/change_password', [UserChangeController::class, 'changeUserPassword'])->name('changeUserPassword');
    Route::post('/update_name', [UserChangeController::class, 'changeName'])->name('changeName');

    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
