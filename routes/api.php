<?php


use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\UserChangeController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('/register', [AuthController::class, 'adminRegister'])->name('adminRegister');
    Route::post('/login', [AuthController::class, 'login'])->name('adminLogin');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/change_password', [UserChangeController::class, 'changeUserPassword'])->name('adminChangeUserPassword');
        Route::post('/update_name', [UserChangeController::class, 'changeName'])->name('adminChangeName');

        Route::get('/all_students', [AuthController::class, 'students'])->name('adminStudents');
        Route::get('/all_teachers', [AuthController::class, 'teachers'])->name('teachers');
        Route::get('/profile', [AuthController::class, 'profile'])->name('adminProfile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('adminLogout');
    });
});

Route::prefix('teacher')->group(function () {
    Route::post('/register', [AuthController::class, 'teacherRegister'])->name('teacherRegister');
    Route::post('/login', [AuthController::class, 'login'])->name('teacherLogin');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/change_password', [UserChangeController::class, 'changeUserPassword'])->name('teacherChangeUserPassword');
        Route::post('/update_name', [UserChangeController::class, 'changeName'])->name('teacherChangeName');

        Route::get('/all_students', [AuthController::class, 'students'])->name('teacherStudents');
        Route::get('/profile', [AuthController::class, 'profile'])->name('teacherProfile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('teacherLogout');
    });
});

Route::post('/register', [AuthController::class, 'studentRegister'])->name('studentRegister');
Route::post('/login', [AuthController::class, 'login'])->name('studentLogin');

Route::get('/success={email}&{token}', [EmailVerificationController::class, 'verifiedSuccess']);
Route::post('/resend_email', [EmailVerificationController::class, 'emailVerificationLink'])->name('resendEmailVerificationLink');
Route::post('/check_verify_email', [EmailVerificationController::class, 'checkVerify'])->name('checkVerify');

Route::post('/send_reset_code', [ResetPasswordController::class, 'sendCodeLink'])->name('sendCodeLink');
Route::post('/check_code_verify', [ResetPasswordController::class, 'checkCodeVerify'])->name('checkCodeVerify');
Route::post('/reset', [ResetPasswordController::class, 'resetPassword'])->name('resetPassword');

Route::get('/auth/{driver}', [SocialLoginController::class, 'toProvider'])->where('driver', 'github|google|facebook');
Route::get('/callback/{driver}/login', [SocialLoginController::class, 'handleCallback'])->where('driver', 'github|google|facebook');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/change_password', [UserChangeController::class, 'changeUserPassword'])->name('studentChangeUserPassword');
    Route::post('/update_name', [UserChangeController::class, 'changeName'])->name('studentChangeName');

    Route::get('/profile', [AuthController::class, 'profile'])->name('studentProfile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('studentLogout');
});
