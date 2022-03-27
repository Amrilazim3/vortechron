<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Post\BookmarkController;
use App\Http\Controllers\Post\CategoryController;
use App\Http\Controllers\Post\CommentController;
use App\Http\Controllers\Post\LikeController;
use App\Http\Controllers\Post\PopularController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\User\Account\EditProfileController;
use App\Http\Controllers\User\Account\ProfileController;
use App\Http\Controllers\User\ChangePasswordController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\FriendController;
use App\Http\Controllers\User\PostController as UserPostController;
use App\Http\Controllers\User\ResetPasswordController;
use App\Http\Controllers\User\SetPasswordController;
use App\Http\Resources\EditPostResource;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;

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
    Route::post('/user/friends/{user}', [FriendController::class, 'follow']);
    Route::delete('/user/friends/unfollow/{user}', [FriendController::class, 'unfollow']);
    Route::get('/user/followers', [FriendController::class, 'followers']);
    Route::post('/user/followers/{user}', [FriendController::class, 'follow']);
    Route::delete('/user/followers/unfollow/{user}', [FriendController::class, 'unfollow']);
    Route::get('/user/following', [FriendController::class, 'following']);
    Route::post('/user/following/{user}', [FriendController::class, 'follow']);
    Route::delete('/user/following/unfollow/{user}', [FriendController::class, 'unfollow']);

    Route::get('/users/{user}', [FriendController::class, 'show']);
    Route::post('/users/{user}', [FriendController::class, 'follow']);
    Route::delete('/users/unfollow/{user}', [FriendController::class, 'unfollow']);
    Route::get('/users/followers/{user}', [FriendController::class, 'showFollowers']);
    Route::get('/users/following/{user}', [FriendController::class, 'showFollowing']);

    Route::get('/user/posts', [UserPostController::class, 'index']);
    Route::get('/user/posts/{post:slug}', [UserPostController::class, 'show']);
    Route::get('/user/posts/create/categories-list', function () {
        $categories = Category::orderBy('name')->get(['name']);
        return response()->json([
            'categories' => $categories
        ]); 
    });
    Route::post('/user/posts/create', [UserPostController::class, 'store']);
    Route::get('/user/posts/edit-post-data/{post:slug}', function (Post $post) {
        return response()->json([
            'post' => new EditPostResource($post)
        ]);
    });
    Route::post('/user/posts/{post:slug}/edit', [UserPostController::class, 'update']);
    Route::delete('/user/posts/{post:slug}/delete', [UserPostController::class, 'destroy']);

    Route::get('/user/liked/posts', [LikeController::class, 'index']);
    Route::get('/user/bookmarked/posts', [BookmarkController::class, 'index']);

    Route::post('/comment/post', [CommentController::class, 'store']);
    Route::delete('/comment/delete/{comment}', [CommentController::class, 'destroy']);

    Route::get('/posts/{post:slug}/{user}/get-like-and-bookmark-condition', function (Post $post, User $user) {
        $LikedPost = Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->exists();

        $bookmarkedPost = Bookmark::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->exists();

        return response()->json([
            'is_liked' => $LikedPost,
            'is_bookmarked' => $bookmarkedPost,
        ]);
    });
    Route::post('/posts/{post:slug}/like', [LikeController::class, 'store']);
    Route::delete('/posts/{post:slug}/{user}/like/delete', [LikeController::class, 'destroy']);

    Route::post('/posts/{post:slug}/bookmark', [BookmarkController::class, 'store']);
    Route::delete('/posts/{post:slug}/{user}/bookmark/delete', [BookmarkController::class, 'destroy']);

});

// route for searching users or posts
Route::get('/users-or-posts', [SearchController::class, '__invoke']);

// show user profile while unauthenticated
Route::get('/users/view-only/{user}', [FriendController::class, 'viewOnly']);

// show all post in dashboard randomly
Route::get('/posts', [PostController::class, 'index']);

// show all posts sort by 'created_at' (latest)
Route::get('/posts/latest', [PostController::class, 'latest']);

// show all popular posts (by liked post)
Route::get('/posts/popular', [PopularController::class, '__invoke']);

// get user single's post
Route::get('/users/posts/{post:slug}', [PostController::class, 'show']);

// get all post categories 
Route::get('/posts/categories', [CategoryController::class, 'index']);

// get all posts by the selected category
Route::get('/posts/categories/{category:slug}', [CategoryController::class, 'show']);

// get all comments by when view single post
Route::get('/{post:slug}/comments', [CommentController::class, 'index']);

// This route can be access by non-authenticated & authenticated users (as long user have password)
Route::post('/user/account/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->name('password.email');

// when user click the link that has been emailed to them
Route::get('/user/account/forgot-password/{token}', [ForgotPasswordController::class, 'handle'])
    ->name('password.reset');

// handle reset password form 
Route::patch('/user/account/reset-password', [ResetPasswordController::class, 'resetPassword']);

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
    
// Resend link to verify email
Route::post('/email/verify/resend', [VerifyEmailController::class, 'resend'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');
