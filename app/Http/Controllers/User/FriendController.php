<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Friend;
use App\Models\User;
use App\Traits\ArrayPaginable;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    use ArrayPaginable;

    public function index(Request $request)
    {
        $user = $request->user();
        $friends = $user->friends();

        return response()->json([
            'friends' => $friends
        ]);
    }

    public function followers(Request $request)
    {
        $authUser = $request->user();
        $resFollowers = $this->paginate(
            $authUser->followers,
            20
        );
        $followers = UserResource::collection($resFollowers)
            ->response()
            ->getData();
            
        $followingIds = $this->paginate(
            $authUser->following->sort()->pluck('id'),
            20
        );

        return response()->json([
            'followers' => $followers,
            'following_ids' => $followingIds
        ]);
    }

    public function following(Request $request)
    {
        $authUser = $request->user();
        $resFollowing = $this->paginate(
            $authUser->following,
            20
        );
        $following = UserResource::collection($resFollowing)
            ->response()
            ->getData();

        $followingsIds = $this->paginate(
            $authUser->following->pluck('id'),
            20
        );

        return response()->json([
            'following' => $following,
            'following_ids' => $followingsIds
        ]);
    }

    public function show(User $user, Request $request)
    {
        $authUser = $request->user();

        $followersCount = $user::withCount('followers')
            ->find($user->id)
            ->followers_count;
        $followingCount = $user::withCount('following')
            ->find($user->id)
            ->following_count;

        $isFriendWithUser = Friend::where('requester', $authUser->id)
                                ->where('user_requested', $user->id)
                                ->where('status', 1)
                                ->exists();

        $userIsFriendWithAuthUser = Friend::where('user_requested', $authUser->id)
                                        ->where('requester', $user->id)
                                        ->where('status', 1)
                                        ->exists();

        return response()->json([
            'user' => $user->only(
                    'id',
                    'name',
                    'username',
                    'image_url',
                    'image_full_url',
                    'bio'
            ),
            'followers_count' => $followersCount,
            'following_count' => $followingCount,
            'is_friend_with_user' => $isFriendWithUser,
            'user_friend_with_auth_user' => $userIsFriendWithAuthUser,
        ]);
    }

    public function viewOnly(User $user)
    {
        $followersCount = $user::withCount('followers')
            ->find($user->id)
            ->followers_count;
        $followingCount = $user::withCount('following')
            ->find($user->id)
            ->following_count;

        return response()->json([
            'user' => $user->only(
                    'id',
                    'name',
                    'username',
                    'image_url',
                    'image_full_url',
                    'bio'
            ),
            'followers_count' => $followersCount,
            'following_count' => $followingCount,
        ]);
    }

    public function showFollowers(User $user, Request $request)
    {
        $authUser = $request->user();

        $resFollowers = $this->paginate(
            $user->followers,
            20
        );
        $followers = UserResource::collection($resFollowers)
            ->response()
            ->getData();
            
        $followingIds = [];

        foreach ($followers as $eachFollower) {
            foreach ($eachFollower as $singleUser) {
                if (is_object($singleUser)) {
                    $isFollowing = Friend::where('user_requested', $singleUser->id)
                        ->where('requester', $authUser['id'])
                        ->exists();
                        
                    if ($isFollowing) {
                        array_push($followingIds, $singleUser->id);
                    }
                }
            }
        }

        return response()->json([
            'username' => $user->username,
            'followers' => $followers,
            'following_ids' => $followingIds
        ]);
    }

    public function showFollowing(User $user, Request $request)
    {
        $authUser = $request->user();

        $resFollowing = $this->paginate(
            $user->following,
            20
        );
        $following = UserResource::collection($resFollowing)
            ->response()
            ->getData();

        $followingsIds = [];

        foreach ($following as $eachFollowing) {
            foreach ($eachFollowing as $singleUser) {
                if (is_object($singleUser)) {
                    $isFollowing = Friend::where('user_requested', $singleUser->id)
                        ->where('requester', $authUser['id'])
                        ->exists();
                        
                    if ($isFollowing) {
                        array_push($followingsIds, $singleUser->id);
                    }
                }
            }
        }

        return response()->json([
            'username' => $user->username,
            'following' => $following,
            'following_ids' => $followingsIds
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
}
