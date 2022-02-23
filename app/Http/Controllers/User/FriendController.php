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
        $followers = $user->followersIds();
        $following = $user->followingIds();
        $friendRequestSentTo = $user->pendingFriendRequestsSentIds();
        return response()->json([
            'friends'  => $friends,
            'followers' => $followers,
            'following' => $following,
            'friend_request_sent_to' => $friendRequestSentTo
        ]);
    }

    public function show(User $user, Request $request)
    {
        $authUser = $request->user();

        $followers = $user->followersIds();
        $following = $user->followingIds();
        $isFriendWith = $authUser->isFriendsWith($user->id);
        $isSentFriendRequestTo = $authUser->hasPendingFriendRequestSentTo($user->id);

        return response()->json([
            'user' => $user,
            'followers' => $followers,
            'following' => $following,
            'is_friend_with' => $isFriendWith,
            'is_sent_friend_request_to' => $isSentFriendRequestTo
        ]);
    }

    public function follow(User $user, Request $request)
    {
        return $request->user()->addFriend($user->id);
    }

    public function cancelRequest(User $user, Request $request)
    {
        return $request->user()->cancelFriendRequestSentTo($user->id);
    }

    public function delete(User $user, Request $request)
    {
        return $request->user()->deleteFriend($user->id);
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
