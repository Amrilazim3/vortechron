<?php

namespace App\Http\Controllers\User\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $followersCount = $user::withCount('followers')
            ->find($user->id)
            ->followers_count;
        $followingCount = $user::withCount('following')
            ->find($user->id)
            ->following_count;

        return response()->json([
            'followers_count' => $followersCount,
            'following_count' => $followingCount
        ]);
    }
}
