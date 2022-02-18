<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\User\Account\EditProfileController;
use App\Http\Controllers\User\ChangePasswordController;
use App\Http\Controllers\User\SetPasswordController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('guest')->group(function() {
    Route::post('/sign-up', [SignUpController::class, 'signUp']);
    Route::post('/sign-in', [SignInController::class, 'signIn']);

    Route::get('oauth/{service}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{service}/callback', [OAuthController::class, 'handleCallback']);
    Route::get('oauth/sign-in/{user}', [OAuthController::class, 'OAuthSignIn']);
});


Route::middleware('auth')->group(function() {
    Route::post('/sign-out', [SignInController::class, 'destroy']);

    Route::post('/user/account/profile/edit', [EditProfileController::class, 'edit']);
    Route::get('/user/account/profile/remove-profile-image', [EditProfileController::class, 'removeFile']);
    Route::post('/user/account/profile/change-email', [EditProfileController::class, 'changeEmail']);
    
    Route::post('/user/account/change-password', [ChangePasswordController::class, 'update']);

    Route::get('/user/account/change-password/get-password', [ChangePasswordController::class, 'getPassword']);
    Route::post('/user/account/set-password', [SetPasswordController::class, 'setPassword']);
});

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Resend link to verify email
Route::post('/email/verify/resend', [VerifyEmailController::class, 'resend'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');
