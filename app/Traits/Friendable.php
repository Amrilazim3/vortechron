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
            array_push($friends, User::find($friendship->user_requested)->only('id', 'username', 'image_url', 'image_full_url'));
        endforeach;
        $friends2 = array();
        $f2 = Friend::where('status', 1)
            ->where('user_requested', $this->id)
            ->get(); // get users that current user already approve to be friend
        foreach($f2 as $friendship):
            array_push($friends2, User::find($friendship->requester)->only('id', 'username', 'image_url', 'image_full_url'));
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
            array_push($followerArr, User::find($follower->requester)->only('id', 'username', 'image_url', 'image_full_url'));
        }

        return $followerArr;
    }

    public function followersIds()
    {
        return collect($this->followers())->pluck('id')->toArray();
    }

    public function following()
    {
        $followingArr = array();
        $followingUsers = Friend::where('requester', $this->id)
            ->where('status', 1)
            ->get();
        foreach ($followingUsers as $following) {
            array_push($followingArr, User::find($following->user_requested)->only('id', 'username', 'image_url', 'image_full_url'));
        }

        return $followingArr;
    }

    public function followingIds()
    {
        return collect($this->following())->pluck('id')->toArray();
    }

    public function follow($user_requested_id) 
    {
        $newFollow = Friend::create([
            'requester' => $this->id,
            'user_requested' => $user_requested_id,
            'status' => 1
        ]);

        if ($newFollow) {
            return 1;
        }
        return 0;
    }

    public function unfollow($user_requested_id)
    {
        $following = Friend::where('user_requested', $user_requested_id)
            ->where('requester', $this->id)
            ->where('status', 1);
        
            if ($following) {
                $following->delete();
                return 1;
            } else{
                return 0;
            }
    }

    // check if current user is friend with other user.
    public function isFriendsWith($id)
    {
        if (in_array($id, $this->followingIds())) {
            return 1;
        } else {
            return 0;
        }
    }
}