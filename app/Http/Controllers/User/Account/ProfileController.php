<?php

namespace App\Http\Controllers\User\Account;

use App\Http\Controllers\Controller;
use App\Jobs\SlowJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $followers = $user->followersIds();
        $following = $user->followingIds();

        return response()->json([
            'followers' => $followers,
            'following' => $following
        ]);
    }
}
