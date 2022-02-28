<?php

namespace App\Http\Controllers\User;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $friends = $user->friends();
        return response()->json([
            'friends'  => $friends,
        ]);
    }

    public function show(User $user, Request $request)
    {
        $authUser = $request->user();

        $followers = $user->followersIds();
        $following = $user->followingIds();
        $isFriendWith = $authUser->isFriendsWith($user->id);
        $userIsFriendWithAuthUser = $user->isFriendsWith($authUser->id);

        return response()->json([
            'user' => $user->only('id', 'name', 'username', 'image_url', 'image_full_url', 'bio'),
            'followers' => $followers,
            'following' => $following,
            'is_friend_with' => $isFriendWith,
            'user_friend_with_auth_user' => $userIsFriendWithAuthUser,
        ]);
    }

    public function viewOnly(User $user)
    {
        $followers = $user->followersIds();
        $following = $user->followingIds();

        return response()->json([
            'user' => $user->only('id', 'name', 'username', 'image_url', 'image_full_url', 'bio'),
            'followers' => $followers,
            'following' => $following,
        ]);
    }

    public function follow(User $user, Request $request)
    {
        return $request->user()->follow($user->id);
    }

    public function unfollow(User $user, Request $request)
    {
        return $request->user()->unfollow($user->id);
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
