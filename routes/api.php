<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\User\Account\EditProfileController;
use App\Http\Controllers\User\Account\ProfileController;
use App\Http\Controllers\User\ChangePasswordController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\FriendController;
use App\Http\Controllers\User\ResetPasswordController;
use App\Http\Controllers\User\SetPasswordController;
use App\Models\Friend;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('guest')->group(function() {
    Route::post('/sign-up', [SignUpController::class, '__invoke']);
    Route::post('/sign-in', [SignInController::class, 'index']);

    Route::get('oauth/{service}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{service}/callback', [OAuthController::class, 'handleCallback']);
    Route::get('oauth/sign-in/{user}', [OAuthController::class, 'OAuthSignIn']);
});

Route::middleware('auth')->group(function() {
    Route::post('/sign-out', [SignInController::class, 'destroy']);

    Route::get('/user/account/profile', [ProfileController::class, 'index']);
    Route::post('/user/account/profile/edit', [EditProfileController::class, 'edit']);
    Route::patch('/user/account/profile/remove-profile-image', [EditProfileController::class, 'removeFile']);
    Route::patch('/user/account/profile/change-email', [EditProfileController::class, 'changeEmail']);
    Route::patch('/user/account/change-password', [ChangePasswordController::class, 'update']);
    Route::get('/user/account/get-password', [ChangePasswordController::class, 'getPassword']);
    Route::patch('/user/account/set-password', [SetPasswordController::class, '__invoke']);
    
    Route::get('/user/friends', [FriendController::class, 'index']);
    Route::delete('/user/friends/{user}', [FriendController::class, 'delete']);
    Route::get('/user/friends/{user}', [FriendController::class, 'show']);
    Route::post('/user/friends/{user}', [FriendController::class, 'follow']);
    Route::delete('/user/friends/cancel-request/{user}', [FriendController::class, 'cancelRequest']);
    Route::delete('/user/friends/unfollow/{user}', [FriendController::class, 'unfollow']);

    Route::get('/users/{user}', [FriendController::class, 'show']);
    Route::patch('/users/accept/{user}', [FriendController::class, 'accept']);
    Route::patch('/users/deny/{user}', [FriendController::class, 'deny']);
    Route::post('/users/{user}', [FriendController::class, 'follow']);
    Route::delete('/users/cancel-request/{user}', [FriendController::class, 'cancelRequest']);
    Route::delete('/users/unfollow/{user}', [FriendController::class, 'unfollow']);
});
// route for getting users and posts 
Route::get('/users', [SearchController::class, 'index']);
Route::get('/users/view-only/{user}', [FriendController::class, 'viewOnly']);

// This route can be access by non-authenticated & authenticated users (as long user have password)
Route::post('/user/account/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->name('password.email');
// when user click link in the email
Route::get('/user/account/forgot-password/{token}', [ForgotPasswordController::class, 'handle'])
    ->name('password.reset');
//handle reset password form 
Route::patch('/user/account/reset-password', [ResetPasswordController::class, 'resetPassword']);

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
// Resend link to verify email
Route::post('/email/verify/resend', [VerifyEmailController::class, 'resend'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');
