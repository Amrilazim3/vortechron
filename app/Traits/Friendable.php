<?php 

namespace App\Traits;

use App\Models\Friend;
use App\Models\User;

trait Friendable {

    public function friends()
    {
        $friends1 = [];
        Friend::where('requester', $this->id)
            ->chunk(10, function ($friends) use (&$friends1) {
                foreach ($friends as $friend) {
                array_push($friends1, $friend->user_requested);
            }
        });

        $friends2 = [];
        Friend::where('user_requested', $this->id)
            ->chunk(10, function ($friends) use (&$friends2) {
                foreach ($friends as $friend) {
                array_push($friends2, $friend->requester);
            }
        });
        
        $realFriends = [];
        $onlyRealFriends = array_intersect($friends1, $friends2);

        foreach ($onlyRealFriends as $friend) {
            array_push($realFriends, User::find($friend)->only('id', 'username', 'image_url', 'image_full_url'));
        }

        return $realFriends;
    }



    public function friendsIds()
    {
        return collect($this->friends())->pluck('id')->toArray();
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

}