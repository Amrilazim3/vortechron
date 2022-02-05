<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

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

    Route::post('/user/profile/edit', [ProfileController::class, 'edit']);
});
