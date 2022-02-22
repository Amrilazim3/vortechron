<?php 

namespace App\Traits;

use App\Models\Friend;
use App\Models\User;

trait Friendable {

    // two way of friends
    public function friends()
    {
        $friends = array();
        $f1 = Friend::where('status', 1)
            ->where('requester', $this->id)
            ->get(); // get users that approve current user friend request
        foreach($f1 as $friendship):
            array_push($friends, User::find($friendship->user_requested));
        endforeach;
        $friends2 = array();
        $f2 = Friend::where('status', 1)
            ->where('user_requested', $this->id)
            ->get(); // get users that current user already approve to be friend
        foreach($f2 as $friendship):
            array_push($friends2, User::find($friendship->requester));
        endforeach;
        return array_merge($friends, $friends2);
    }

    // get only 'id' column from this.friends() result.
    public function friendsIds()
    {
        return collect($this->friends())->pluck('id')->toArray();
    }

    public function followers()
    {
        $followerArr = array();
        $followers = Friend::where('user_requested', $this->id)
            ->where('status', 1)
            ->get();
        foreach ($followers as $follower) {
            array_push($followerArr, User::find($follower->requester));
        }

        return $followerArr;
    }

    public function followersIds()
    {
        return collect($this->followers())->pluck('id')->toArray();
    }

    public function followings()
    {
        $followingArr = array();
        $followings = Friend::where('requester', $this->id)
            ->where('status', 1)
            ->get();
        foreach ($followings as $following) {
            array_push($followingArr, User::find($following->user_requested));
        }

        return $followingArr;
    }

    public function followingsIds()
    {
        return collect($this->followings())->pluck('id')->toArray();
    }

    // other users is pending request for current user.
    public function pendingFriendRequests()
    {
        $users = array();
        $friendships = Friend::where('status', 0)
            ->where('user_requested', $this->id)
            ->get();
        foreach($friendships as $friendship):
            array_push($users, User::find($friendship->requester));
        endforeach;
        return $users;
    }

    // get only 'id' column from result of this.pendingFriendRequests().
    public function pendingFriendRequestsIds()
    {
		return collect($this->pendingFriendRequests())->pluck('id')->toArray();
    }

    // check if the given '$user_id' exists in pendingFriendRequestsIds()
	public function hasPendingFriendRequestFrom($user_id)
    {
		if(in_array($user_id, $this->pendingFriendRequestsIds())) {
			return 1;
		}
		else {
			return 0;
		}
    }

    // the current user is pendng friend request to other user.
    public function pendingFriendRequestsSent()
    {
		$users = array();
		$friendships = Friend::where('status', 0)
            ->where('requester', $this->id)
            ->get();
		foreach($friendships as $friendship):
			array_push($users, User::find($friendship->user_requested));
		endforeach;
		return $users;
	}

    // get only 'id' column from this.pendingFriendRequestsSent()
	public function pendingFriendRequestsSentIds() 
    {
		return collect($this->pendingFriendRequestsSent())->pluck('id')->toArray();
	}

    // check if current user is making friend request to other users.
    public function hasPendingFriendRequestSentTo($user_id)
    {
		if(in_array($user_id, $this->pendingFriendRequestsSentIds())) {
			return 1;
		}
		else {
			return 0;
		}
    }

    public function cancelFriendRequestSentTo($user_requested_id)
    {
        if ($this->hasPendingFriendRequestSentTo($user_requested_id) == 1) {
            $friendToCancel = Friend::where('user_requested', $user_requested_id)
                ->where('status', 0);

            $friendToCancel->delete();
            return 1;
        } else {
            return 0;
        }
    }

    // check if current user is friend with other user.
    public function isFriendsWith($id)
    {
        if (in_array($id, $this->followingsIds())) {
            return 1;
        } else {
            return 0;
        }
    }

    // checking for current user before friend with other users.
    public function addFriend($user_requested_id)
    {
        if($this->id === $user_requested_id) {
			return 0;
        }
        if($this->isFriendsWith($user_requested_id) === 1) {
			return "already friends";
        }

        // current user already sent from request but the requested user not accept yet
        if($this->hasPendingFriendRequestSentTo($user_requested_id) === 1){ 
			return "already sent a friend request";
        }

        // check if other users also has sent friend request automatic add as friend
        // because both request to be friend with each other.
        if($this->hasPendingFriendRequestFrom($user_requested_id) === 1){
			return $this->acceptFriend($user_requested_id);
		}
        $friendship = Friend::create([
            'requester' => $this->id,
            'user_requested' => $user_requested_id,
            'status' => 0
        ]);
        if($friendship) {
			return 1;
		}
		return 0;
    }

    // making a requester become friend with the current user.
    public function acceptFriend($requester) 
    {
        if($this->hasPendingFriendRequestFrom($requester) === 0) {
            return 0;
        }
        $friendship = Friend::where('requester', $requester)
            ->where('user_requested', $this->id)
            ->first();
        if($friendship) {
            $friendship->update([
                'status' => 1
            ]);
            return 1;
        }
        return 0;
    }

    // deny the request from other users to be friend with current user.
    public function denyFriend($requester)
    {
        if($this->hasPendingFriendRequestFrom($requester) === 0) {
            return 0;
        }
        $friendship = Friend::where('requester', $requester)
            ->where('user_requested', $this->id)
            ->first();
        if($friendship) {
            $friendship->delete();
            return 1;
        }
        return 0;
    }

    public function deleteFriend($user_requested_id)
    {
		if($this->id === $user_requested_id) {
            return 0;
        }
		if(in_array($user_requested_id, $this->followersIds()) || in_array($user_requested_id, $this->followingsIds())) {
            $Friendship1 = Friend::where('requester', $user_requested_id)
            ->where('user_requested', $this->id)
            ->first();
            if ($Friendship1) {
                $Friendship1->delete();
            }
            $Friendship2 = Friend::where('user_requested', $user_requested_id)
            ->where('requester', $this->id)
            ->first();
            if ($Friendship2) {
                $Friendship2->delete();
            }
        }
    }
}